<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use BVP\Trimmer\Trimmer;
use BVP\Scraper\Traits\HttpBrowserInitializer;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
abstract class BaseScraper implements BaseScraperInterface
{
    use HttpBrowserInitializer;

    /**
     * @psalm-var non-empty-string
     *
     * @var string
     */
    protected string $baseUrl = 'https://www.boatrace.jp';

    /**
     * @psalm-var int<0, 1>
     *
     * @var int
     */
    protected int $baseLevel = 0;

    /**
     * @psalm-var int<0, max>
     *
     * @var int
     */
    protected int $seconds = 1;

    /**
     * @psalm-param \Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     *
     * @param \Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     */
    final public function __construct(protected readonly HttpBrowser $httpBrowser)
    {
        $this->initializeHttpBrowser($httpBrowser);
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?string
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?string
     */
    protected function filterXPath(Crawler $scraper, string $xpath): ?string
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->text();
        $value = Converter::convertToString($value);
        $value = Trimmer::trim($value);
        return $value;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?string
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?string
     */
    protected function filterXPathRaw(Crawler $scraper, string $xpath): ?string
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        return $scraper->filterXPath($xpath)->text();
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?string
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?string
     */
    protected function filterXPathForGradeLabel(Crawler $scraper, string $xpath): ?string
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->attr('class');
        $value = Converter::convertToString($value);
        $value = Trimmer::trim($value);

        if ($value === null) {
            return null;
        }

        if (preg_match('/is-([a-zA-Z0-9]+)/', $value, $matches)) {
            if ($matches[1] === 'ippan') {
                return '一般';
            }

            return substr($matches[1], 0, 2);
        }

        return null;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?int
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?int
     */
    protected function filterXPathForGradeNumber(Crawler $scraper, string $xpath): ?int
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->attr('class');
        $value = Converter::convertToString($value);
        $value = Trimmer::trim($value);

        if ($value === null) {
            return null;
        }

        if (preg_match('/is-([a-zA-Z0-9]+)/', $value, $matches)) {
            if ($matches[1] === 'ippan') {
                return 5;
            }

            return match (substr($matches[1], 0, 2)) {
                'SG' => 1,
                'G1' => 2,
                'G2' => 3,
                'G3' => 4,
            };
        }

        return null;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?string
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?string
     */
    protected function filterXPathForWindDirectionNumber(Crawler $scraper, string $xpath): ?string
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->attr('class');
        $value = Converter::convertToString($value);
        $value = Trimmer::trim($value);
        return $value;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return ?float
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?float
     */
    protected function filterXPathForOdds(Crawler $scraper, string $xpath): ?float
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->text();
        $value = Converter::convertToFloat($value);
        return $value;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param string $xpath
     * @psalm-return array{
     *     lower_limit: ?float,
     *     upper_limit: ?float,
     * }
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return array
     */
    protected function filterXPathForOddsWithLowerLimitAndUpperLimit(Crawler $scraper, string $xpath): array
    {
        $response = ['lower_limit' => null, 'upper_limit' => null];

        if ($scraper->filterXPath($xpath)->count()) {
            if (count($oddses = explode('-', $scraper->filterXPath($xpath)->text())) === 2) {
                $response['lower_limit'] = Converter::convertToFloat(array_shift($oddses));
                $response['upper_limit'] = Converter::convertToFloat(array_shift($oddses));
            }
        }

        return $response;
    }
}
