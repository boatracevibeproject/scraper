<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
class StadiumScraper extends BaseScraper implements StadiumScraperInterface
{
    /**
     * @param  \Carbon\CarbonInterface  $raceDate
     * @return array
     */
    public function scrape(CarbonInterface $raceDate): array
    {
        $response = [];

        $scraperFormat = '%s/owpc/pc/race/index?hd=%s';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $raceDate->format('Ymd'));
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);

        $response = [];
        $scraper = $scraper->filter('.table1')->eq(0);
        $scraper = $scraper->filter('table tbody td.is-arrow1.is-fBold.is-fs15');
        $scraper->each(function ($element) use (&$response) {
            $stadiumName = str_replace('>', '', $element->filter('a')->filter('img')->attr('alt'));
            $stadiumNumber = Converter::convertToStadiumNumber($stadiumName);
            $response[$stadiumNumber] = $stadiumName;
        });

        return $response;
    }
}
