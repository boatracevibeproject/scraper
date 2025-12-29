<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class StadiumScraper extends BaseScraper implements StadiumScraperInterface
{
    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-return array<int<1, 24>, non-empty-string>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @return array
     */
    #[\Override]
    public function scrape(CarbonInterface $raceDate): array
    {
        $scraperFormat = '%s/owpc/pc/race/index?hd=%s';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $raceDate->format('Ymd'));
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        $scraper = $scraper->filter('.table1')->eq(0);
        $scraper = $scraper->filter('table tbody td.is-arrow1.is-fBold.is-fs15');
        $stadiums = $scraper->each(function (Crawler $element): array {
            $stadiumName = $element->filter('a')->filter('img')->attr('alt');
            if ($stadiumName === null || $stadiumName === '') {
                return [];
            }

            $stadiumName = str_replace('>', '', $stadiumName);
            if ($stadiumName === '') {
                return [];
            }

            $stadiumNumber = Converter::convertToStadiumNumber($stadiumName);
            if ($stadiumNumber === null) {
                return [];
            }

            return [$stadiumNumber => $stadiumName];
        });

        $response = [];
        foreach ($stadiums as $stadium) {
            foreach ($stadium as $number => $name) {
                $response[$number] = $name;
            }
        }

        return $response;
    }
}
