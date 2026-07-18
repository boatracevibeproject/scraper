<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\Contracts\StadiumScraper as StadiumScraperContract;
use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Enums\Stadium;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class StadiumScraper extends BaseScraper implements StadiumScraperContract
{
    /**
     * @param \Carbon\CarbonInterface $date
     * @return array<int<1, 24>, non-empty-string>
     */
    #[\Override]
    public function scrape(CarbonInterface $date): array
    {
        $scraperFormat = '%s/owpc/pc/race/index?hd=%s';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'));
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);
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

            $stadium = Converter::toEnumOrNull(fn() => Stadium::fromName($stadiumName));
            if ($stadium === null) {
                return [];
            }

            return [$stadium->value => $stadiumName];
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
