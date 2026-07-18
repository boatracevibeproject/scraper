<?php

declare(strict_types=1);

namespace BVP\Scraper\Filters;

use BVP\Scraper\Converters\Converter;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class OddsFilter
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?float
     */
    public static function byXPath(Crawler $scraper, string $xpath): ?float
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->text();

        return Converter::toFloat($value);
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return array{
     *     lower_limit: ?float,
     *     upper_limit: ?float,
     * }
     */
    public static function byXPathAsRange(Crawler $scraper, string $xpath): array
    {
        $response = ['lower_limit' => null, 'upper_limit' => null];

        if ($scraper->filterXPath($xpath)->count()) {
            if (count($odds = explode('-', $scraper->filterXPath($xpath)->text())) === 2) {
                $response['lower_limit'] = Converter::toFloat(array_shift($odds));
                $response['upper_limit'] = Converter::toFloat(array_shift($odds));
            }
        }

        return $response;
    }
}
