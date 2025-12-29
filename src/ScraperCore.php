<?php

declare(strict_types=1);

namespace BVP\Scraper;

use BVP\Converter\Converter;
use BVP\Scraper\Scrapers\BaseScraperInterface;
use BVP\Scraper\Scrapers\OddsScraper;
use BVP\Scraper\Scrapers\PreviewScraper;
use BVP\Scraper\Scrapers\ProgramScraper;
use BVP\Scraper\Scrapers\ResultScraper;
use BVP\Scraper\Scrapers\StadiumScraper;
use BVP\Scraper\Scrapers\StadiumScraperInterface;
use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @psalm-import-type ScraperArguments from \BVP\Scraper\Scraper
 * @psalm-method array<array-key, mixed> scrapePrograms(mixed ...$arguments)
 * @psalm-method array<array-key, mixed> scrapePreviews(mixed ...$arguments)
 * @psalm-method array<array-key, mixed> scrapeOdds(mixed ...$arguments)
 * @psalm-method array<array-key, mixed> scrapeResults(mixed ...$arguments)
 * @psalm-method array<array-key, mixed> scrapeStadiums(mixed ...$arguments)
 *
 * @author shimomo
 */
final class ScraperCore implements ScraperCoreInterface
{
    /**
     * @psalm-var array<non-empty-string, \BVP\Scraper\Scrapers\BaseScraperInterface>
     *
     * @var array
     */
    private array $instances = [];

    /**
     * @psalm-var non-empty-array<non-empty-string, class-string<\BVP\Scraper\Scrapers\BaseScraperInterface>>
     *
     * @var array
     */
    private array $scraperClasses = [
        'scrapeOdds' => OddsScraper::class,
        'scrapeWinOdds' => OddsScraper::class,
        'scrapePlaceOdds' => OddsScraper::class,
        'scrapeExactaOdds' => OddsScraper::class,
        'scrapeQuinellaOdds' => OddsScraper::class,
        'scrapeQuinellaPlaceOdds' => OddsScraper::class,
        'scrapeTrifectaOdds' => OddsScraper::class,
        'scrapeTrioOdds' => OddsScraper::class,
        'scrapePreviews' => PreviewScraper::class,
        'scrapePrograms' => ProgramScraper::class,
        'scrapeResults' => ResultScraper::class,
        'scrapeStadiums' => StadiumScraper::class,
    ];

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param ScraperArguments $arguments
     * @psalm-return array<array-key, mixed>
     *
     * @param string $name
     * @param array $arguments
     * @return array
     * @throws \InvalidArgumentException
     */
    public function __call(string $name, array $arguments): array
    {
        $arguments = array_slice($arguments, 0, 3);

        /**
         * @psalm-var array{
         *   0?: CarbonInterface|string|null,
         *   1?: int<1,24>|string|null,
         *   2?: int<1,12>|string|null,
         * } $arguments
         */
        return $this->scraper($name, ...$arguments);
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param \Carbon\CarbonInterface|string $raceDate
     * @psalm-param int<1, 24>|string|null $raceStadiumNumber
     * @psalm-param int<1, 12>|string|null $raceNumber
     * @psalm-return array<array-key, mixed>
     *
     * @param string $name
     * @param \Carbon\CarbonInterface|string $raceDate
     * @param int|string|null $raceStadiumNumber
     * @param int|string|null $raceNumber
     * @return array
     */
    private function scraper(
        string $name,
        CarbonInterface|string|null $raceDate = null,
        int|string|null $raceStadiumNumber = null,
        int|string|null $raceNumber = null
    ): array {
        $raceDate = Carbon::parse($raceDate ?? 'today');

        if ($name === 'scrapeStadiums') {
            return $this->getStadiumScraper()->scrape($raceDate);
        }

        $scraper = $this->getScraperInstance($name);

        $raceStadiumNumbers = $this->getRaceStadiumNumbers($raceDate, $raceStadiumNumber);
        $raceNumbers = $this->getRaceNumbers($raceNumber);

        $response = [];
        foreach ($raceStadiumNumbers as $raceStadiumNumber) {
            foreach ($raceNumbers as $raceNumber) {
                if (method_exists($scraper, 'scrape')) {
                    $response[$raceStadiumNumber][$raceNumber] = $this->callWithRetry(
                        function () use ($scraper, $raceDate, $raceStadiumNumber, $raceNumber): array {
                            /** @psalm-var array<non-empty-string, mixed> */
                            return $scraper->scrape($raceDate, $raceStadiumNumber, $raceNumber);
                        }
                    );
                } elseif (preg_match('/^scrape([a-zA-Z]+)Odds$/u', $name, $matches)) {
                    $response[$raceStadiumNumber][$raceNumber] = $this->callWithRetry(
                        function () use ($scraper, $matches, $raceDate, $raceStadiumNumber, $raceNumber): array {
                            /** @psalm-var array<non-empty-string, mixed> */
                            return $scraper->{'scrape' . $matches[1]}($raceDate, $raceStadiumNumber, $raceNumber);
                        }
                    );
                }
            }
        }

        return $response;
    }

    /**
     * @psalm-param string $name
     * @psalm-return class-string<\BVP\Scraper\Scrapers\BaseScraperInterface>
     *
     * @param string $name
     * @return string
     * @throws \BadMethodCallException
     */
    private function resolveScraperClass(string $name): string
    {
        if (isset($this->scraperClasses[$name])) {
            return $this->scraperClasses[$name];
        }

        throw new \BadMethodCallException(
            __METHOD__ . "() - Scraper name for `{$name}` is invalid."
        );
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-return \BVP\Scraper\Scrapers\BaseScraperInterface
     *
     * @param string $name
     * @return \BVP\Scraper\Scrapers\BaseScraperInterface
     */
    private function getScraperInstance(string $name): BaseScraperInterface
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        return $this->createScraperInstance($name);
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-return \BVP\Scraper\Scrapers\BaseScraperInterface
     *
     * @param string $name
     * @return \BVP\Scraper\Scrapers\BaseScraperInterface
     */
    private function createScraperInstance(string $name): BaseScraperInterface
    {
        $scraper = $this->resolveScraperClass($name);
        return $this->instances[$name] = new $scraper(
            new HttpBrowser()
        );
    }

    /**
     * @psalm-return \BVP\Scraper\Scrapers\StadiumScraperInterface
     *
     * @return \BVP\Scraper\Scrapers\StadiumScraperInterface
     * @throws \LogicException
     */
    private function getStadiumScraper(): StadiumScraperInterface
    {
        $stadiumScraper = $this->getScraperInstance('scrapeStadiums');
        if ($stadiumScraper instanceof StadiumScraperInterface) {
            return $stadiumScraper;
        }

        $stadiumScraperClassName = get_class($stadiumScraper);

        throw new \LogicException(
            __METHOD__ . "() - Stadium scraper instance for `{$stadiumScraperClassName}` is invalid."
        );
    }

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int|string|null $raceStadiumNumber
     * @psalm-return non-empty-list<int<1, 24>>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int|string|null $raceStadiumNumber
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getRaceStadiumNumbers(CarbonInterface $raceDate, int|string|null $raceStadiumNumber): array
    {
        if ($raceStadiumNumber === null) {
            $raceStadiumNumbers = array_keys($this->getStadiumScraper()->scrape($raceDate));

            if (!count($raceStadiumNumbers)) {
                /** @psalm-var non-empty-list<int<1, 24>> */
                return range(1, 24);
            }

            return $raceStadiumNumbers;
        }

        $formattedRaceStadiumNumber = Converter::convertToString($raceStadiumNumber);
        if ($formattedRaceStadiumNumber === null) {
            $raceStadiumNumbers = array_keys($this->getStadiumScraper()->scrape($raceDate));

            if (!count($raceStadiumNumbers)) {
                /** @psalm-var non-empty-list<int<1, 24>> */
                return range(1, 24);
            }

            return $raceStadiumNumbers;
        }

        if (preg_match('/\b(0?[1-9]|1[0-9]|2[0-4])\b/', $formattedRaceStadiumNumber, $matches)) {
            $raceStadiumNumber = (int) $matches[1];

            if ($raceStadiumNumber <= 0 || $raceStadiumNumber >= 25) {
                throw new \InvalidArgumentException(
                    __METHOD__ . "() - Race Stadium number for `{$raceStadiumNumber}` is invalid."
                );
            }

            return [$raceStadiumNumber];
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - Race stadium number for `{$raceStadiumNumber}` is invalid."
        );
    }

    /**
     * @psalm-param int|string|null $raceNumber
     * @psalm-return non-empty-list<int<1, 12>>
     *
     * @param int|string|null $raceNumber
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getRaceNumbers(int|string|null $raceNumber): array
    {
        if ($raceNumber === null) {
            /** @psalm-var non-empty-list<int<1, 12>> */
            return range(1, 12);
        }

        $formattedRaceNumber = Converter::convertToString($raceNumber);
        if ($formattedRaceNumber === null) {
            /** @psalm-var non-empty-list<int<1, 12>> */
            return range(1, 12);
        }

        if (preg_match('/\b(0?[1-9]|1[0-2])\b/', $formattedRaceNumber, $matches)) {
            $raceNumber = (int) $matches[1];

            if ($raceNumber <= 0 || $raceNumber >= 13) {
                throw new \InvalidArgumentException(
                    __METHOD__ . "() - Race number for `{$raceNumber}` is invalid."
                );
            }

            return [$raceNumber];
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - Race number for `{$raceNumber}` is invalid."
        );
    }

    /**
     * @psalm-param callable $callback
     * @psalm-param int<0, max> $maxRetries
     * @psalm-param int<0, max> $retryDelaySeconds
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param callable $callback
     * @param int $maxRetries
     * @param int $retryDelaySeconds
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function callWithRetry(
        callable $callback,
        int $maxRetries = 3,
        int $retryDelaySeconds = 3
    ): mixed {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                /** @psalm-var array<non-empty-string, mixed> */
                return $callback();
            } catch (\RuntimeException $exception) {
                if ($attempt >= $maxRetries) {
                    throw $exception;
                }

                sleep($retryDelaySeconds);
            }
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - Retry count `{$maxRetries}` is invalid."
        );
    }
}
