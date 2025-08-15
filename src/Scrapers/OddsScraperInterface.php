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
     * @param  \Carbon\CarbonInterface  $raceDate
     * @param  int                      $raceStadiumNumber
     * @param  int                      $raceNumber
     * @return array
     */
    public function scrape(CarbonInterface $raceDate, int $raceStadiumNumber, int $raceNumber): array;
}
