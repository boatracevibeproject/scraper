<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use BVP\Scraper\Scrapers\StadiumScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @psalm-import-type RaceDate from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceStadiumNumber from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class StadiumScraperTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @psalm-var \BVP\Scraper\Scrapers\StadiumScraper
     *
     * @var \BVP\Scraper\Scrapers\StadiumScraper
     */
    protected StadiumScraper $scraper;

    /**
     * @psalm-return void
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->scraper = new StadiumScraper(
            new HttpBrowser()
        );
    }

    /**
     * @psalm-param array{RaceDate} $arguments
     * @psalm-param array<RaceStadiumNumber, non-empty-string> $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(StadiumScraperDataProvider::class, 'scrapeStadiumsProvider')]
    public function testScrapeStadiums(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrape(...$arguments));
    }
}
