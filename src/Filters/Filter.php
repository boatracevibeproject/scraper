<?php

declare(strict_types=1);

namespace BVP\Scraper\Filters;

use BVP\Scraper\Converters\Converter;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class Filter
{
    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param string $xpath
     * @return ?string
     */
    public static function byXPath(Crawler $scraper, string $xpath): ?string
    {
        if (!$scraper->filterXPath($xpath)->count()) {
            return null;
        }

        $value = $scraper->filterXPath($xpath)->text();

        $value = Converter::toKana($value);

        return Converter::trim($value);
    }
}
