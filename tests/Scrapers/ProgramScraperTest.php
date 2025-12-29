<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use BVP\Scraper\Scrapers\ProgramScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @psalm-import-type RaceArguments from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceExpected from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class ProgramScraperTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @psalm-var \BVP\Scraper\Scrapers\ProgramScraper
     *
     * @var \BVP\Scraper\Scrapers\ProgramScraper
     */
    protected ProgramScraper $scraper;

    /**
     * @psalm-return void
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->scraper = new ProgramScraper(
            new HttpBrowser()
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
    #[DataProviderExternal(ProgramScraperDataProvider::class, 'scrapeProvider')]
    public function testScrape(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrape(...$arguments));
    }
}
