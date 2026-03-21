<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests;

use BVP\Scraper\ScraperDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-import-type RaceDate from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceStadiumNumber from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceArguments from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceExpectedByStadium from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class ScraperDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @psalm-var \BVP\Scraper\ScraperDispatcher
     *
     * @var \BVP\Scraper\ScraperDispatcher
     */
    protected ScraperDispatcher $scraper;

    /**
     * @psalm-return void
     *
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->scraper = new ScraperDispatcher();
    }

    /**
     * @psalm-param RaceArguments $arguments
     * @psalm-param RaceExpectedByStadium $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(ScraperDataProvider::class, 'scrapeOddsesProvider')]
    public function testScrapeOddses(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeOdds(...$arguments));
    }

    /**
     * @psalm-param RaceArguments $arguments
     * @psalm-param RaceExpectedByStadium $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(ScraperDataProvider::class, 'scrapePreviewsProvider')]
    public function testScrapePreviews(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapePreviews(...$arguments));
    }

    /**
     * @psalm-param RaceArguments $arguments
     * @psalm-param RaceExpectedByStadium $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(ScraperDataProvider::class, 'scrapeProgramsProvider')]
    public function testScrapePrograms(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapePrograms(...$arguments));
    }

    /**
     * @psalm-param RaceArguments $arguments
     * @psalm-param RaceExpectedByStadium $expected
     * @psalm-return void
     *
     * @param array $arguments
     * @param array $expected
     * @return void
     */
    #[DataProviderExternal(ScraperDataProvider::class, 'scrapeResultsProvider')]
    public function testScrapeResults(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeResults(...$arguments));
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
    #[DataProviderExternal(ScraperDataProvider::class, 'scrapeStadiumsProvider')]
    public function testScrapeStadiums(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrapeStadiums(...$arguments));
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testThrowsExceptionWhenMethodDoesNotExist(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            "BVP\Scraper\ScraperDispatcher::resolveScraperClass() - " .
            "Scraper name for `ghost` is invalid."
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->scraper->ghost('2017-03-31', 24, 1);
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testThrowsExceptionWhenRaceStadiumNumberIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "BVP\Scraper\ScraperDispatcher::getRaceStadiumNumbers() - " .
            "Race stadium number for `#` is invalid."
        );

        $this->scraper->scrapePrograms('2017-03-31', '#', 1);
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testThrowsExceptionWhenRaceNumberIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "BVP\Scraper\ScraperDispatcher::getRaceNumbers() - " .
            "Race number for `#` is invalid."
        );

        $this->scraper->scrapePrograms('2017-03-31', 24, '#');
    }
}
