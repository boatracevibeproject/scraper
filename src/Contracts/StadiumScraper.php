<?php

declare(strict_types=1);

namespace BVP\Scraper\Contracts;

use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface StadiumScraper
{
    /**
     * @param \Carbon\CarbonInterface $date
     * @return array<int<1, 24>, non-empty-string>
     */
    public function scrape(CarbonInterface $date): array;
}
