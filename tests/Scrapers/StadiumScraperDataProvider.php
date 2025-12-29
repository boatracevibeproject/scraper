<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use Carbon\CarbonImmutable as Carbon;

/**
 * @psalm-import-type RaceDate from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceStadiumNumber from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class StadiumScraperDataProvider
{
    /**
     * @psalm-return non-empty-list<
     *     array{
     *         arguments: array{RaceDate},
     *         expected: array<RaceStadiumNumber, non-empty-string>,
     *     }
     * >
     *
     * @return array
     */
    public static function scrapeStadiumsProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31')],
                'expected' => [
                    4 => '平和島',
                    5 => '多摩川',
                    6 => '浜名湖',
                    10 => '三国',
                    15 => '丸亀',
                    18 => '徳山',
                    23 => '唐津',
                    24 => '大村',
                ],
            ],
        ];
    }
}
