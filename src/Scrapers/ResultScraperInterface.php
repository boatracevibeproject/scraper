<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\ScraperContractInterface;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface ResultScraperInterface extends ScraperContractInterface
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
}
