<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\Contracts\Scraper;
use BVP\Scraper\Filters\Filter;
use BVP\Scraper\Filters\OddsFilter;
use BVP\Scraper\RateLimiting\RateLimiterInterface;
use Carbon\CarbonInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Odds are spread across five distinct boatrace.jp pages (trifecta, trio,
 * exacta+quinella together, quinella-place, win+place together), so a full
 * scrape() issues five HTTP requests. Unlike the other scrapers, this one
 * needs its own rate limiter reference to pace those five requests against
 * each other, not just against whatever scrape*() call preceded it.
 *
 * @author shimomo
 */
final class OddsScraper extends BaseScraper implements Scraper
{
    /**
     * @var non-empty-string
     */
    private string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @var int<0, 1>
     */
    private int $baseLevel = 0;

    /**
     * @param \Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param \BVP\Scraper\RateLimiting\RateLimiterInterface $rateLimiter
     */
    public function __construct(HttpBrowser $httpBrowser, private readonly RateLimiterInterface $rateLimiter)
    {
        parent::__construct($httpBrowser);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrape(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $response = [];

        $response += $this->scrapeTrifecta($date, $stadiumNumber, $raceNumber);
        $this->rateLimiter->throttle();
        $response += $this->scrapeTrio($date, $stadiumNumber, $raceNumber);
        $this->rateLimiter->throttle();
        $response += $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber);
        $this->rateLimiter->throttle();
        $response += $this->scrapeQuinellaPlace($date, $stadiumNumber, $raceNumber);
        $this->rateLimiter->throttle();
        $response += $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber);

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeTrifecta(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/odds3t?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $this->resolveBaseLevel($scraper);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        // [first, second, third, row, column] for all 120 permutations of 1-6 taken 3 at a time.
        $trifectaTemplates = [
            [1, 2, 3, 1, 3], [1, 2, 4, 2, 2], [1, 2, 5, 3, 2], [1, 2, 6, 4, 2],
            [1, 3, 2, 5, 3], [1, 3, 4, 6, 2], [1, 3, 5, 7, 2], [1, 3, 6, 8, 2],
            [1, 4, 2, 9, 3], [1, 4, 3, 10, 2], [1, 4, 5, 11, 2], [1, 4, 6, 12, 2],
            [1, 5, 2, 13, 3], [1, 5, 3, 14, 2], [1, 5, 4, 15, 2], [1, 5, 6, 16, 2],
            [1, 6, 2, 17, 3], [1, 6, 3, 18, 2], [1, 6, 4, 19, 2], [1, 6, 5, 20, 2],
            [2, 1, 3, 1, 6], [2, 1, 4, 2, 4], [2, 1, 5, 3, 4], [2, 1, 6, 4, 4],
            [2, 3, 1, 5, 6], [2, 3, 4, 6, 4], [2, 3, 5, 7, 4], [2, 3, 6, 8, 4],
            [2, 4, 1, 9, 6], [2, 4, 3, 10, 4], [2, 4, 5, 11, 4], [2, 4, 6, 12, 4],
            [2, 5, 1, 13, 6], [2, 5, 3, 14, 4], [2, 5, 4, 15, 4], [2, 5, 6, 16, 4],
            [2, 6, 1, 17, 6], [2, 6, 3, 18, 4], [2, 6, 4, 19, 4], [2, 6, 5, 20, 4],
            [3, 1, 2, 1, 9], [3, 1, 4, 2, 6], [3, 1, 5, 3, 6], [3, 1, 6, 4, 6],
            [3, 2, 1, 5, 9], [3, 2, 4, 6, 6], [3, 2, 5, 7, 6], [3, 2, 6, 8, 6],
            [3, 4, 1, 9, 9], [3, 4, 2, 10, 6], [3, 4, 5, 11, 6], [3, 4, 6, 12, 6],
            [3, 5, 1, 13, 9], [3, 5, 2, 14, 6], [3, 5, 4, 15, 6], [3, 5, 6, 16, 6],
            [3, 6, 1, 17, 9], [3, 6, 2, 18, 6], [3, 6, 4, 19, 6], [3, 6, 5, 20, 6],
            [4, 1, 2, 1, 12], [4, 1, 3, 2, 8], [4, 1, 5, 3, 8], [4, 1, 6, 4, 8],
            [4, 2, 1, 5, 12], [4, 2, 3, 6, 8], [4, 2, 5, 7, 8], [4, 2, 6, 8, 8],
            [4, 3, 1, 9, 12], [4, 3, 2, 10, 8], [4, 3, 5, 11, 8], [4, 3, 6, 12, 8],
            [4, 5, 1, 13, 12], [4, 5, 2, 14, 8], [4, 5, 3, 15, 8], [4, 5, 6, 16, 8],
            [4, 6, 1, 17, 12], [4, 6, 2, 18, 8], [4, 6, 3, 19, 8], [4, 6, 5, 20, 8],
            [5, 1, 2, 1, 15], [5, 1, 3, 2, 10], [5, 1, 4, 3, 10], [5, 1, 6, 4, 10],
            [5, 2, 1, 5, 15], [5, 2, 3, 6, 10], [5, 2, 4, 7, 10], [5, 2, 6, 8, 10],
            [5, 3, 1, 9, 15], [5, 3, 2, 10, 10], [5, 3, 4, 11, 10], [5, 3, 6, 12, 10],
            [5, 4, 1, 13, 15], [5, 4, 2, 14, 10], [5, 4, 3, 15, 10], [5, 4, 6, 16, 10],
            [5, 6, 1, 17, 15], [5, 6, 2, 18, 10], [5, 6, 3, 19, 10], [5, 6, 4, 20, 10],
            [6, 1, 2, 1, 18], [6, 1, 3, 2, 12], [6, 1, 4, 3, 12], [6, 1, 5, 4, 12],
            [6, 2, 1, 5, 18], [6, 2, 3, 6, 12], [6, 2, 4, 7, 12], [6, 2, 5, 8, 12],
            [6, 3, 1, 9, 18], [6, 3, 2, 10, 12], [6, 3, 4, 11, 12], [6, 3, 5, 12, 12],
            [6, 4, 1, 13, 18], [6, 4, 2, 14, 12], [6, 4, 3, 15, 12], [6, 4, 5, 16, 12],
            [6, 5, 1, 17, 18], [6, 5, 2, 18, 12], [6, 5, 3, 19, 12], [6, 5, 4, 20, 12],
        ];

        foreach ($trifectaTemplates as [$first, $second, $third, $row, $column]) {
            $xPathFormat = '%s/div[2]/div[%d]/table/tbody/tr[%d]/td[%d]';
            $xPath = sprintf($xPathFormat, $this->baseXPath, $this->baseLevel + 7, $row, $column);
            $response['trifecta'][$first][$second][$third] = OddsFilter::byXPath($scraper, $xPath);
        }

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeTrio(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/odds3f?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $this->resolveBaseLevel($scraper);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        $trioXPath = sprintf('%s/div[2]/div[%d]/table/tbody/tr[1]/td[3]', $this->baseXPath, $this->baseLevel + 7);
        $response['trio'][1][2][3] = OddsFilter::byXPath($scraper, $trioXPath);

        $trioTemplates = [
            [1, 2, 4, '%s/div[2]/div[%d]/table/tbody/tr[2]/td[2]'],
            [1, 2, 5, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[2]'],
            [1, 2, 6, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[2]'],
            [1, 3, 4, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[3]'],
            [1, 3, 5, '%s/div[2]/div[%d]/table/tbody/tr[6]/td[2]'],
            [1, 3, 6, '%s/div[2]/div[%d]/table/tbody/tr[7]/td[2]'],
            [1, 4, 5, '%s/div[2]/div[%d]/table/tbody/tr[8]/td[3]'],
            [1, 4, 6, '%s/div[2]/div[%d]/table/tbody/tr[9]/td[2]'],
            [1, 5, 6, '%s/div[2]/div[%d]/table/tbody/tr[10]/td[3]'],
            [2, 3, 4, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[6]'],
            [2, 3, 5, '%s/div[2]/div[%d]/table/tbody/tr[6]/td[4]'],
            [2, 3, 6, '%s/div[2]/div[%d]/table/tbody/tr[7]/td[4]'],
            [2, 4, 5, '%s/div[2]/div[%d]/table/tbody/tr[8]/td[6]'],
            [2, 4, 6, '%s/div[2]/div[%d]/table/tbody/tr[9]/td[4]'],
            [2, 5, 6, '%s/div[2]/div[%d]/table/tbody/tr[10]/td[6]'],
            [3, 4, 5, '%s/div[2]/div[%d]/table/tbody/tr[8]/td[9]'],
            [3, 4, 6, '%s/div[2]/div[%d]/table/tbody/tr[9]/td[6]'],
            [3, 5, 6, '%s/div[2]/div[%d]/table/tbody/tr[10]/td[9]'],
            [4, 5, 6, '%s/div[2]/div[%d]/table/tbody/tr[10]/td[12]'],
        ];

        foreach ($trioTemplates as [$first, $second, $third, $format]) {
            $xPath = sprintf($format, $this->baseXPath, $this->baseLevel + 7);
            $response['trio'][$first][$second][$third] = OddsFilter::byXPath($scraper, $xPath);
        }

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeExacta(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeQuinella(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeExactaAndQuinella($date, $stadiumNumber, $raceNumber);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeExactaAndQuinella(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/odds2tf?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $this->resolveBaseLevel($scraper);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (range(1, 6) as $first) {
            foreach (range(1, 6) as $second) {
                if ($first === $second) {
                    continue;
                }

                $row = $first < $second ? $second - 1 : $second;
                $column = $first * 2;

                $exactaFormat = '%s/div[2]/div[%d]/table/tbody/tr[%d]/td[%d]';
                $exactaXPath = sprintf($exactaFormat, $this->baseXPath, $this->baseLevel + 7, $row, $column);
                $response['exacta'][$first][$second] = OddsFilter::byXPath($scraper, $exactaXPath);
            }
        }

        $quinellaTemplates = [
            [1, 2, '%s/div[2]/div[%d]/table/tbody/tr[1]/td[2]'],
            [1, 3, '%s/div[2]/div[%d]/table/tbody/tr[2]/td[2]'],
            [1, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[2]'],
            [1, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[2]'],
            [1, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[2]'],
            [2, 3, '%s/div[2]/div[%d]/table/tbody/tr[2]/td[4]'],
            [2, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[4]'],
            [2, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[4]'],
            [2, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[4]'],
            [3, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[6]'],
            [3, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[6]'],
            [3, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[6]'],
            [4, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[8]'],
            [4, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[8]'],
            [5, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[10]'],
        ];

        foreach ($quinellaTemplates as [$first, $second, $format]) {
            $xPath = sprintf($format, $this->baseXPath, $this->baseLevel + 9);
            $response['quinella'][$first][$second] = OddsFilter::byXPath($scraper, $xPath);
        }

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeQuinellaPlace(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/oddsk?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $this->resolveBaseLevel($scraper);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        $quinellaPlaceTemplates = [
            [1, 2, '%s/div[2]/div[%d]/table/tbody/tr[1]/td[2]'],
            [1, 3, '%s/div[2]/div[%d]/table/tbody/tr[2]/td[2]'],
            [1, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[2]'],
            [1, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[2]'],
            [1, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[2]'],
            [2, 3, '%s/div[2]/div[%d]/table/tbody/tr[2]/td[4]'],
            [2, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[4]'],
            [2, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[4]'],
            [2, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[4]'],
            [3, 4, '%s/div[2]/div[%d]/table/tbody/tr[3]/td[6]'],
            [3, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[6]'],
            [3, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[6]'],
            [4, 5, '%s/div[2]/div[%d]/table/tbody/tr[4]/td[8]'],
            [4, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[8]'],
            [5, 6, '%s/div[2]/div[%d]/table/tbody/tr[5]/td[10]'],
        ];

        foreach ($quinellaPlaceTemplates as [$first, $second, $format]) {
            $xPath = sprintf($format, $this->baseXPath, $this->baseLevel + 7);
            $response['quinella_place'][$first][$second] = OddsFilter::byXPathAsRange($scraper, $xPath);
        }

        return $response;
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeWin(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapePlace(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        return $this->scrapeWinAndPlace($date, $stadiumNumber, $raceNumber);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    public function scrapeWinAndPlace(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/oddstf?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $this->resolveBaseLevel($scraper);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        foreach (range(1, 6) as $boatNumber) {
            $winFormat = '%s/div[2]/div[%d]/div[1]/div[2]/table/tbody[%d]/tr/td[3]';
            $winXPath = sprintf($winFormat, $this->baseXPath, $this->baseLevel + 6, $boatNumber);
            $response['win'][$boatNumber] = OddsFilter::byXPath($scraper, $winXPath);

            $placeFormat = '%s/div[2]/div[%d]/div[2]/div[2]/table/tbody[%d]/tr/td[3]';
            $placeXPath = sprintf($placeFormat, $this->baseXPath, $this->baseLevel + 6, $boatNumber);
            $response['place'][$boatNumber] = OddsFilter::byXPathAsRange($scraper, $placeXPath);
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return void
     */
    private function resolveBaseLevel(Crawler $scraper): void
    {
        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (Filter::byXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }
    }
}
