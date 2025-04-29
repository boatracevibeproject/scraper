<?php

declare(strict_types=1);

namespace BVP\BoatraceScraper\Tests\Scrapers;

use BVP\BoatraceScraper\Scrapers\StadiumScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class StadiumScraperTest extends TestCase
{
    /**
     * @var \BVP\BoatraceScraper\Scrapers\StadiumScraper
     */
    protected StadiumScraper $scraper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scraper = new StadiumScraper(
            new HttpBrowser()
        );
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(StadiumScraperDataProvider::class, 'scrapeStadiumsProvider')]
    public function testScrapeStadiums(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrape(...$arguments));
    }
}
