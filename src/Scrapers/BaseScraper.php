<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
abstract class BaseScraper
{
    /**
     * @var non-empty-string
     */
    protected string $baseUrl = 'https://www.boatrace.jp';

    /**
     * @param \Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     */
    public function __construct(protected readonly HttpBrowser $httpBrowser)
    {
        //
    }

    /**
     * Clears the cookie jar before every request. boatrace.jp throttles more
     * aggressively when it sees a returning session cookie, so a stateless
     * request per call is deliberate, not an oversight.
     *
     * @param string $method
     * @param string $url
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function requestAndClearCookies(string $method, string $url): Crawler
    {
        $this->httpBrowser->getCookieJar()->clear();

        return $this->httpBrowser->request($method, $url);
    }
}
