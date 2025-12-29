<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\ScraperContractInterface;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface OddsScraperInterface extends ScraperContractInterface
{
    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrape(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeWin(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapePlace(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeExacta(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeQuinella(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeQuinellaPlace(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeTrifecta(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    public function scrapeTrio(
        CarbonInterface $raceDate,
        int $raceStadiumNumber,
        int $raceNumber
    ): array;
}
