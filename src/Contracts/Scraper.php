<?php

declare(strict_types=1);

namespace BVP\Scraper\Contracts;

use Carbon\CarbonInterface;

/**
 * Shared by the four scrapers that operate on a single race
 * (Program/Preview/Odds/Result). Stadium scraping has its own contract
 * ({@see StadiumScraper}) since it takes no race/stadium numbers.
 *
 * @author shimomo
 */
interface Scraper
{
    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrape(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array;
}
