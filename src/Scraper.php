<?php

declare(strict_types=1);

namespace BVP\Scraper;

use BVP\Scraper\Caching\CacheFactory;
use BVP\Scraper\Caching\CacheKeyFactory;
use BVP\Scraper\Caching\CachePolicyInterface;
use BVP\Scraper\Caching\DateBasedCachePolicy;
use BVP\Scraper\Factories\HttpBrowserFactory;
use BVP\Scraper\RateLimiting\RateLimiterInterface;
use BVP\Scraper\RateLimiting\ThrottleRateLimiter;
use BVP\Scraper\Retry\RetryPolicy;
use BVP\Scraper\Scrapers\OddsScraper;
use BVP\Scraper\Scrapers\PreviewScraper;
use BVP\Scraper\Scrapers\ProgramScraper;
use BVP\Scraper\Scrapers\ResultScraper;
use BVP\Scraper\Scrapers\StadiumScraper;
use BVP\Scraper\Validators\Validator;
use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * Instance-based entry point: every dependency (HttpBrowser, rate limiter,
 * cache, cache policy, retry policy) is scoped to the constructed instance,
 * so multiple instances (e.g. one per proxy/worker) never share pacing or
 * cache state. There is deliberately no static singleton facade — construct
 * as many instances as your use case needs.
 *
 * @author shimomo
 */
final class Scraper
{
    /**
     * @var \Symfony\Component\BrowserKit\HttpBrowser
     */
    private readonly HttpBrowser $httpBrowser;

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private readonly CacheInterface $cache;

    /**
     * @var \BVP\Scraper\Scrapers\ResultScraper
     */
    private readonly ResultScraper $resultScraper;

    /**
     * @var \BVP\Scraper\Scrapers\StadiumScraper
     */
    private readonly StadiumScraper $stadiumScraper;

    /**
     * @var \BVP\Scraper\Scrapers\ProgramScraper
     */
    private readonly ProgramScraper $programScraper;

    /**
     * @var \BVP\Scraper\Scrapers\PreviewScraper
     */
    private readonly PreviewScraper $previewScraper;

    /**
     * @var \BVP\Scraper\Scrapers\OddsScraper
     */
    private readonly OddsScraper $oddsScraper;

    /**
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param \BVP\Scraper\RateLimiting\RateLimiterInterface $rateLimiter
     * @param ?\Psr\SimpleCache\CacheInterface $cache
     * @param \BVP\Scraper\Caching\CachePolicyInterface $cachePolicy
     * @param \BVP\Scraper\Retry\RetryPolicy $retryPolicy
     */
    public function __construct(
        ?HttpBrowser $httpBrowser = null,
        private readonly RateLimiterInterface $rateLimiter = new ThrottleRateLimiter(),
        ?CacheInterface $cache = null,
        private readonly CachePolicyInterface $cachePolicy = new DateBasedCachePolicy(),
        private readonly RetryPolicy $retryPolicy = new RetryPolicy(),
    ) {
        $this->httpBrowser = $httpBrowser ?? HttpBrowserFactory::create();
        $this->cache = $cache ?? CacheFactory::createDefault();

        $this->resultScraper = new ResultScraper($this->httpBrowser);
        $this->stadiumScraper = new StadiumScraper($this->httpBrowser);
        $this->programScraper = new ProgramScraper($this->httpBrowser);
        $this->previewScraper = new PreviewScraper($this->httpBrowser);
        $this->oddsScraper = new OddsScraper($this->httpBrowser, $this->rateLimiter);
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeOdds(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        Validator::validateStadiumNumber($stadiumNumber);
        Validator::validateRaceNumber($raceNumber);

        $parsedDate = Carbon::parse($date);

        return $this->fetch(
            'odds',
            $parsedDate,
            $stadiumNumber,
            $raceNumber,
            fn(): array => $this->oddsScraper->scrape($parsedDate, $stadiumNumber, $raceNumber),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeWin(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('win', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapeWin(...));
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapePlace(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('place', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapePlace(...));
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeExacta(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('exacta', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapeExacta(...));
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeQuinella(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('quinella', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapeQuinella(...));
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeQuinellaPlace(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType(
            'quinella_place',
            $date,
            $stadiumNumber,
            $raceNumber,
            $this->oddsScraper->scrapeQuinellaPlace(...),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeTrifecta(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('trifecta', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapeTrifecta(...));
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeTrio(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeOddsBetType('trio', $date, $stadiumNumber, $raceNumber, $this->oddsScraper->scrapeTrio(...));
    }

    /**
     * @param non-empty-string $betType
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param callable(CarbonInterface, int<1, 24>, int<1, 12>): array<non-empty-string, mixed> $fetch
     * @return array<non-empty-string, mixed>
     */
    private function scrapeOddsBetType(
        string $betType,
        CarbonInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        callable $fetch,
    ): array {
        Validator::validateStadiumNumber($stadiumNumber);
        Validator::validateRaceNumber($raceNumber);

        $parsedDate = Carbon::parse($date);

        return $this->fetch(
            'odds',
            $parsedDate,
            $stadiumNumber,
            $raceNumber,
            fn(): array => $fetch($parsedDate, $stadiumNumber, $raceNumber),
            $betType,
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapePreview(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        Validator::validateStadiumNumber($stadiumNumber);
        Validator::validateRaceNumber($raceNumber);

        $parsedDate = Carbon::parse($date);

        return $this->fetch(
            'preview',
            $parsedDate,
            $stadiumNumber,
            $raceNumber,
            fn(): array => $this->previewScraper->scrape($parsedDate, $stadiumNumber, $raceNumber),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeProgram(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        Validator::validateStadiumNumber($stadiumNumber);
        Validator::validateRaceNumber($raceNumber);

        $parsedDate = Carbon::parse($date);

        return $this->fetch(
            'program',
            $parsedDate,
            $stadiumNumber,
            $raceNumber,
            fn(): array => $this->programScraper->scrape($parsedDate, $stadiumNumber, $raceNumber),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @return array<int<1, 24>, non-empty-string>
     */
    public function scrapeStadium(CarbonInterface|string $date): array
    {
        $parsedDate = Carbon::parse($date);

        $cacheKey = CacheKeyFactory::makeForStadium($parsedDate);
        $cacheable = $this->cachePolicy->isCacheable('stadium', $parsedDate);

        if ($cacheable) {
            /** @var ?array<int<1, 24>, non-empty-string> $cached */
            $cached = $this->cache->get($cacheKey);

            if ($cached !== null) {
                return $cached;
            }
        }

        $this->rateLimiter->throttle();

        /** @var array<int<1, 24>, non-empty-string> $result */
        $result = $this->retryPolicy->run(fn(): array => $this->stadiumScraper->scrape($parsedDate));

        if ($cacheable) {
            $this->cache->set($cacheKey, $result);
        }

        return $result;
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeResultBulk(CarbonInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = []): array
    {
        return $this->bulk(
            $date,
            $stadiumNumbers,
            $raceNumbers,
            fn(CarbonInterface $d, int $s, int $r): array => $this->scrapeResult($d, $s, $r),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeProgramBulk(CarbonInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = []): array
    {
        return $this->bulk(
            $date,
            $stadiumNumbers,
            $raceNumbers,
            fn(CarbonInterface $d, int $s, int $r): array => $this->scrapeProgram($d, $s, $r),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapePreviewBulk(CarbonInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = []): array
    {
        return $this->bulk(
            $date,
            $stadiumNumbers,
            $raceNumbers,
            fn(CarbonInterface $d, int $s, int $r): array => $this->scrapePreview($d, $s, $r),
        );
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    public function scrapeOddsBulk(CarbonInterface|string $date, array $stadiumNumbers = [], array $raceNumbers = []): array
    {
        return $this->bulk(
            $date,
            $stadiumNumbers,
            $raceNumbers,
            fn(CarbonInterface $d, int $s, int $r): array => $this->scrapeOdds($d, $s, $r),
        );
    }

    /**
     * Resolves $stadiumNumbers (defaulting to all 24) against the stadiums
     * actually racing on $date, then fans $scrapeOne out across the
     * resulting stadium/race grid. Every call still routes through the
     * single-race scrape*() methods above, so cache/rate-limiter/retry
     * behavior is uniform whether called one race at a time or in bulk.
     *
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param list<int<1, 24>> $stadiumNumbers
     * @param list<int<1, 12>> $raceNumbers
     * @param callable(CarbonInterface, int<1, 24>, int<1, 12>): array<non-empty-string, mixed> $scrapeOne
     * @return array<int<1, 24>, array<int<1, 12>, array<non-empty-string, mixed>>>
     */
    private function bulk(CarbonInterface|string $date, array $stadiumNumbers, array $raceNumbers, callable $scrapeOne): array
    {
        $parsedDate = Carbon::parse($date);

        /** @var list<int<1, 24>> $candidateStadiumNumbers */
        $candidateStadiumNumbers = array_unique($stadiumNumbers ?: range(1, 24));
        /** @var list<int<1, 12>> $uniqueRaceNumbers */
        $uniqueRaceNumbers = array_unique($raceNumbers ?: range(1, 12));

        /** @var list<int<1, 24>> $activeStadiumNumbers */
        $activeStadiumNumbers = array_keys($this->scrapeStadium($parsedDate));
        $stadiumNumbersToScrape = array_intersect($candidateStadiumNumbers, $activeStadiumNumbers);

        $response = [];
        foreach ($stadiumNumbersToScrape as $stadiumNumber) {
            foreach ($uniqueRaceNumbers as $raceNumber) {
                $response[$stadiumNumber][$raceNumber] = $scrapeOne($parsedDate, $stadiumNumber, $raceNumber);
            }
        }

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeResult(CarbonInterface|string $date, int $stadiumNumber, int $raceNumber): array
    {
        Validator::validateStadiumNumber($stadiumNumber);
        Validator::validateRaceNumber($raceNumber);

        $parsedDate = Carbon::parse($date);

        return $this->fetch(
            'result',
            $parsedDate,
            $stadiumNumber,
            $raceNumber,
            fn(): array => $this->resultScraper->scrape($parsedDate, $stadiumNumber, $raceNumber),
        );
    }

    /**
     * Shared cache/rate-limit/retry pipeline every scrape*() method routes
     * through: a cache hit skips both the network call and the rate
     * limiter entirely; a miss throttles, fetches with retry, and (when the
     * cache policy allows it) stores the result forever.
     *
     * @param non-empty-string $type
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param callable(): array<non-empty-string, mixed> $fetch
     * @param ?non-empty-string $betType
     * @return array<non-empty-string, mixed>
     */
    private function fetch(
        string $type,
        CarbonInterface $date,
        int $stadiumNumber,
        int $raceNumber,
        callable $fetch,
        ?string $betType = null,
    ): array {
        $cacheKey = CacheKeyFactory::make($type, $date, $stadiumNumber, $raceNumber, $betType);
        $cacheable = $this->cachePolicy->isCacheable($type, $date);

        if ($cacheable) {
            /** @var ?array<non-empty-string, mixed> $cached */
            $cached = $this->cache->get($cacheKey);

            if ($cached !== null) {
                return $cached;
            }
        }

        $this->rateLimiter->throttle();

        /** @var array<non-empty-string, mixed> $result */
        $result = $this->retryPolicy->run($fetch);

        if ($cacheable) {
            $this->cache->set($cacheKey, $result);
        }

        return $result;
    }
}
