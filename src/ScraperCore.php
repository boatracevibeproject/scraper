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
use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
class ScraperCore implements ScraperCoreInterface
{
    /**
     * @var array
     */
    private array $instances = [];

    /**
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
     * @param  string  $name
     * @param  array   $arguments
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function __call(string $name, array $arguments): array
    {
        $countArguments = count($arguments);
        if ($countArguments >= 4) {
            throw new \InvalidArgumentException(
                __METHOD__ . "() - Too many arguments to function " . self::class . "::{$name}(), " .
                "{$countArguments} passed and exactly 1-3 expected."
            );
        }

        return $this->scraper($name, ...$arguments);
    }

    /**
     * @param  string                          $name
     * @param  \Carbon\CarbonInterface|string  $raceDate
     * @param  string|int|null                 $raceStadiumNumber
     * @param  string|int|null                 $raceNumber
     * @return array
     */
    private function scraper(
        string $name,
        CarbonInterface|string $raceDate,
        int|string|null $raceStadiumNumber = null,
        int|string|null $raceNumber = null
    ): array {
        $scraper = $this->getScraperInstance($name);

        $raceDate = Carbon::parse($raceDate);

        if ($name === 'scrapeStadiums') {
            return $scraper->scrape($raceDate);
        }

        $raceStadiumNumbers = $this->getRaceStadiumNumbers($raceDate, $raceStadiumNumber);
        $raceNumbers = $this->getRaceNumbers($raceNumber);

        $response = [];
        foreach ($raceStadiumNumbers as $raceStadiumNumber) {
            foreach ($raceNumbers as $raceNumber) {
                $response[$raceStadiumNumber][$raceNumber] = $this->callWithRetry(
                    function () use ($scraper, $name, $raceDate, $raceStadiumNumber, $raceNumber) {
                        if (preg_match('/^scrape([a-zA-Z]+)Odds$/u', $name, $matches)) {
                            return $scraper->{'scrape' . $matches[1]}(
                                $raceDate,
                                $raceStadiumNumber,
                                $raceNumber
                            );
                        } else {
                            return $scraper->scrape(
                                $raceDate,
                                $raceStadiumNumber,
                                $raceNumber
                            );
                        }
                    }
                );
            }
        }

        return $response;
    }

    /**
     * @param  string  $name
     * @return string
     *
     * @throws \BadMethodCallException
     */
    private function resolveScraperClass(string $name): string
    {
        if (isset($this->scraperClasses[$name])) {
            return $this->scraperClasses[$name];
        }

        throw new \BadMethodCallException(
            __METHOD__ . "() - The scraper name for '{$name}' is invalid."
        );
    }

    /**
     * @param  string  $name
     * @return \BVP\Scraper\ScraperContractInterface
     */
    private function getScraperInstance(string $name): ScraperContractInterface
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $scraper = $this->resolveScraperClass($name);
        return $this->instances[$name] = new $scraper(
            new HttpBrowser()
        );
    }

    /**
     * @param  string  $name
     * @return \BVP\Scraper\ScraperContractInterface
     */
    private function createScraperInstance(string $name): ScraperContractInterface
    {
        $scraper = $this->resolveScraperClass($name);
        return $this->instances[$name] = new $scraper(
            new HttpBrowser()
        );
    }

    /**
     * @param  \Carbon\CarbonInterface  $raceDate
     * @param  string|int|null          $raceStadiumNumber
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function getRaceStadiumNumbers(CarbonInterface $raceDate, string|int|null $raceStadiumNumber): array
    {
        if ($raceStadiumNumber === null) {
            return array_keys(
                $this->getScraperInstance('scrapeStadiums')->scrape($raceDate)
            );
        }

        $formattedRaceStadiumNumber = Converter::convertToString($raceStadiumNumber);
        if (preg_match('/\b(0?[1-9]|1[0-9]|2[0-4])\b/', $formattedRaceStadiumNumber, $matches)) {
            return [(int) $matches[1]];
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - The race stadium number for '{$raceStadiumNumber}' is invalid."
        );
    }

    /**
     * @param  string|int|null  $raceNumber
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function getRaceNumbers(int|string|null $raceNumber): array
    {
        if ($raceNumber === null) {
            return range(1, 12);
        }

        $formattedRaceNumber = Converter::convertToString($raceNumber);
        if (preg_match('/\b(0?[1-9]|1[0-2])\b/', $formattedRaceNumber, $matches)) {
            return [(int) $matches[1]];
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - The race number for '{$raceNumber}' is invalid."
        );
    }

    /**
     * @param  callable  $callback
     * @param  int       $maxRetries
     * @param  int       $retryDelaySeconds
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
                return $callback();
            } catch (\RuntimeException $exception) {
                if ($attempt >= $maxRetries) {
                    throw $exception;
                }

                sleep($retryDelaySeconds);
            }
        }

        throw new \InvalidArgumentException(
            __METHOD__ . "() - Invalid retry count."
        );
    }
}
