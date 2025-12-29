<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\ScraperContractInterface;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface PreviewScraperInterface extends ScraperContractInterface
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
}
