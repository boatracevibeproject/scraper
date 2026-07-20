<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use RuntimeException;
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
     * Root container shared by every page this library requests (race list,
     * before-info, odds, race result, and the stadium index). Used both to
     * build each scraper's own field XPaths and, via
     * {@see self::requestAndAssertPage()}, as a structural sanity check.
     *
     * @var non-empty-string
     */
    protected string $baseXPath = 'descendant-or-self::body/main/div/div/div';

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

    /**
     * Same as {@see self::requestAndClearCookies()}, but also verifies the
     * response has boatrace.jp's basic page skeleton before returning it.
     * Symfony's HttpBrowser never throws on non-2xx responses, so without
     * this check a maintenance/error page (a different template entirely)
     * would silently parse into an all-null result — for a past date, that
     * garbage would then be cached forever. Throwing \RuntimeException here
     * lets RetryPolicy retry the fetch, and if attempts are exhausted, the
     * exception propagates before anything is written to the cache.
     *
     * @param string $method
     * @param string $url
     * @return \Symfony\Component\DomCrawler\Crawler
     * @throws \RuntimeException
     */
    protected function requestAndAssertPage(string $method, string $url): Crawler
    {
        $crawler = $this->requestAndClearCookies($method, $url);

        if (!$crawler->filterXPath($this->baseXPath)->count()) {
            throw new RuntimeException(
                __METHOD__ . "() - Unexpected page structure for `{$url}`; " .
                'boatrace.jp may be under maintenance or returned an error page.'
            );
        }

        return $crawler;
    }
}
