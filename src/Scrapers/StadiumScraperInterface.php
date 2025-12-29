<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\ScraperContractInterface;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface StadiumScraperInterface extends ScraperContractInterface
{
    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-return array<int<1, 24>, non-empty-string>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @return array
     */
    public function scrape(CarbonInterface $raceDate): array;
}
