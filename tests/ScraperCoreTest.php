<?php

declare(strict_types=1);

namespace BVP\BoatraceScraper\Tests;

use BVP\BoatraceScraper\ScraperCore;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ScraperCoreTest extends TestCase
{
    /**
     * @var \BVP\BoatraceScraper\ScraperCore
     */
    protected ScraperCore $scraper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scraper = new ScraperCore();
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ScraperCoreDataProvider::class, 'scrapeOddsesProvider')]
    public function testScrapeOddses(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeOdds(...$arguments));
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ScraperCoreDataProvider::class, 'scrapePreviewsProvider')]
    public function testScrapePreviews(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapePreviews(...$arguments));
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ScraperCoreDataProvider::class, 'scrapeProgramsProvider')]
    public function testScrapePrograms(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapePrograms(...$arguments));
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ScraperCoreDataProvider::class, 'scrapeResultsProvider')]
    public function testScrapeResults(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeResults(...$arguments));
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ScraperCoreDataProvider::class, 'scrapeStadiumsProvider')]
    public function testScrapeStadiums(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeStadiums(...$arguments));
    }

    /**
     * @return void
     */
    public function testExceptionOnTooFewArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "BVP\BoatraceScraper\ScraperCore::__call() - " .
            "Too many arguments to function BVP\BoatraceScraper\ScraperCore::invalid(), " .
            "4 passed and exactly 1-3 expected."
        );

        $this->scraper->invalid(1, 2, 3, 4);
    }

    /**
     * @return void
     */
    public function testExceptionOnDoesNotExistMethodCall(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            "BVP\BoatraceScraper\ScraperCore::resolveScraperClass() - " .
            "The scraper name for 'invalid' is invalid."
        );

        $this->scraper->invalid('2017-03-31', 24, 1);
    }

    /**
     * @return void
     */
    public function testExceptionOnInvalidRaceStadiumNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "BVP\BoatraceScraper\ScraperCore::getRaceStadiumNumbers() - " .
            "The race stadium number for '#' is invalid."
        );

        $this->scraper->scrapePrograms('2017-03-31', '#', 1);
    }

    /**
     * @return void
     */
    public function testExceptionOnInvalidRaceNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "BVP\BoatraceScraper\ScraperCore::getRaceNumbers() - " .
            "The race number for '#' is invalid."
        );

        $this->scraper->scrapePrograms('2017-03-31', 24, '#');
    }
}
