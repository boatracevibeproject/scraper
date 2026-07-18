<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use BVP\Scraper\RateLimiting\ThrottleRateLimiter;
use BVP\Scraper\Scrapers\OddsScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @psalm-import-type RaceArguments from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceExpected from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class OddsScraperTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @psalm-var \BVP\Scraper\Scrapers\OddsScraper
     *
     * @var \BVP\Scraper\Scrapers\OddsScraper
     */
    protected OddsScraper $scraper;

    /**
     * @psalm-return void
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->scraper = new OddsScraper(
            new HttpBrowser(),
            new ThrottleRateLimiter(1.0),
        );
    }

    /**
     * @psalm-param RaceArguments $arguments
     * @psalm-param RaceExpected $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(OddsScraperDataProvider::class, 'scrapeProvider')]
    public function testScrape(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrape(...$arguments));
    }
}
