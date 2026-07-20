<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests;

use BVP\Scraper\Caching\CacheFactory;
use BVP\Scraper\Caching\CacheKeyFactory;
use BVP\Scraper\Retry\RetryPolicy;
use BVP\Scraper\Scraper;
use Carbon\CarbonImmutable as Carbon;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Regression test for the caching-poisoning concern: when boatrace.jp returns
 * a page without its usual skeleton (maintenance/error page on a different
 * template), the scrape must raise instead of silently caching garbage.
 *
 * @author shimomo
 */
final class BrokenPageTest extends TestCase
{
    public function testScrapeResultThrowsAndLeavesTheCacheEmptyWhenThePageIsBroken(): void
    {
        $cacheDir = sys_get_temp_dir() . '/bvp-scraper-broken-page-test-' . uniqid();
        $cache = CacheFactory::createDefault($cacheDir);

        $brokenHtml = '<html><body>ただいまメンテナンス中です</body></html>';
        $httpBrowser = new HttpBrowser(new MockHttpClient(new MockResponse($brokenHtml)));

        $scraper = new Scraper(
            httpBrowser: $httpBrowser,
            cache: $cache,
            retryPolicy: new RetryPolicy(maxAttempts: 1, retryDelaySeconds: 0),
        );

        $date = Carbon::parse('2017-03-31');
        $stadiumNumber = 24;
        $raceNumber = 1;
        $cacheKey = CacheKeyFactory::make('result', $date, $stadiumNumber, $raceNumber);

        $this->expectException(RuntimeException::class);

        try {
            $scraper->scrapeResult($date, $stadiumNumber, $raceNumber);
        } finally {
            $this->assertNull($cache->get($cacheKey), 'A broken-page fetch must not be cached.');
        }
    }
}
