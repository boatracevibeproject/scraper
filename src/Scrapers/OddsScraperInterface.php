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
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrape(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeWin(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapePlace(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeSingle(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeExacta(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeQuinella(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapePair(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeQuinellaPlace(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeTrifecta(CarbonInterface $date, int $stadiumNumber, int $number): array;

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    public function scrapeTrio(CarbonInterface $date, int $stadiumNumber, int $number): array;
}
