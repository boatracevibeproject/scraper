<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests;

use BVP\Scraper\Scraper;
use BVP\Scraper\ScraperInterface;
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
final class ScraperTest extends TestCase
{
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
        $this->assertSame($expected, Scraper::scrapeOdds(...$arguments));
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
        $this->assertSame($expected, Scraper::scrapePreviews(...$arguments));
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
        $this->assertSame($expected, Scraper::scrapePrograms(...$arguments));
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
        $this->assertSame($expected, Scraper::scrapeResults(...$arguments));
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
        $this->assertSame($expected, Scraper::scrapeStadiums(...$arguments));
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
            "BVP\Scraper\ScraperCore::resolveScraperClass() - " .
            "Scraper name for `ghost` is invalid."
        );

        /** @psalm-suppress UndefinedMagicMethod */
        Scraper::ghost('2017-03-31', 24, 1);
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
            "BVP\Scraper\ScraperCore::getRaceStadiumNumbers() - " .
            "Race stadium number for `#` is invalid."
        );

        Scraper::scrapePrograms('2017-03-31', '#', 1);
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
            "BVP\Scraper\ScraperCore::getRaceNumbers() - " .
            "Race number for `#` is invalid."
        );

        Scraper::scrapePrograms('2017-03-31', 24, '#');
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testGetInstance(): void
    {
        Scraper::resetInstance();
        $this->assertInstanceOf(ScraperInterface::class, Scraper::getInstance());
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testCreateInstance(): void
    {
        Scraper::resetInstance();
        $this->assertInstanceOf(ScraperInterface::class, Scraper::createInstance());
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    public function testResetInstance(): void
    {
        Scraper::resetInstance();
        $instance1 = Scraper::getInstance();
        Scraper::resetInstance();
        $instance2 = Scraper::getInstance();
        $this->assertNotSame($instance1, $instance2);
    }
}
