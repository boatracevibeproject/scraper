<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use BVP\Scraper\Scrapers\BaseScraper;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author shimomo
 */
final class BaseScraperTest extends TestCase
{
    public function testRequestAndAssertPageReturnsCrawlerWhenSkeletonIsPresent(): void
    {
        $html = '<html><body><main><div><div><div>content</div></div></div></main></body></html>';
        $httpBrowser = new HttpBrowser(new MockHttpClient(new MockResponse($html)));
        $scraper = new TestableBaseScraper($httpBrowser);

        $crawler = $scraper->callRequestAndAssertPage('GET', 'https://example.test/ok');

        $this->assertGreaterThan(
            0,
            $crawler->filterXPath('descendant-or-self::body/main/div/div/div')->count(),
        );
    }

    public function testRequestAndAssertPageThrowsWhenSkeletonIsMissing(): void
    {
        $html = '<html><body>ただいまメンテナンス中です</body></html>';
        $httpBrowser = new HttpBrowser(new MockHttpClient(new MockResponse($html)));
        $scraper = new TestableBaseScraper($httpBrowser);

        $this->expectException(RuntimeException::class);

        $scraper->callRequestAndAssertPage('GET', 'https://example.test/broken');
    }
}

/**
 * Thin test double exposing the otherwise-protected requestAndAssertPage()
 * so it can be exercised directly against a MockHttpClient response.
 *
 * @author shimomo
 */
final class TestableBaseScraper extends BaseScraper
{
    public function callRequestAndAssertPage(string $method, string $url): Crawler
    {
        return $this->requestAndAssertPage($method, $url);
    }
}
