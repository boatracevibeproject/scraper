<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Scrapers;

use Carbon\CarbonImmutable as Carbon;

/**
 * @psalm-import-type RaceArguments from \BVP\Scraper\Tests\ScraperPsalmType
 * @psalm-import-type RaceExpected from \BVP\Scraper\Tests\ScraperPsalmType
 *
 * @author shimomo
 */
final class ResultScraperDataProvider
{
    /**
     * @psalm-return non-empty-list<
     *     array{
     *         arguments: RaceArguments,
     *         expected: RaceExpected,
     *     }
     * >
     *
     * @return array
     */
    public static function scrapeProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31'), 24, 1],
                'expected' => [
                    'date' => '2017-03-31',
                    'stadium_number' => 24,
                    'number' => 1,
                    'wind_speed' => 5,
                    'wind_direction_number' => 11,
                    'wave_height' => 4,
                    'weather_number' => 3,
                    'air_temperature' => 13.0,
                    'water_temperature' => 14.0,
                    'technique_number' => 1,
                    'boats' => [
                        1 => [
                            'racer_boat_number' => 1,
                            'racer_course_number' => 1,
                            'racer_start_timing' => 0.25,
                            'racer_place_number' => 1,
                            'racer_number' => 3833,
                            'racer_name' => '中辻 博訓',
                        ],
                        2 => [
                            'racer_boat_number' => 2,
                            'racer_course_number' => 2,
                            'racer_start_timing' => 0.28,
                            'racer_place_number' => 2,
                            'racer_number' => 3773,
                            'racer_name' => '津留 浩一郎',
                        ],
                        3 => [
                            'racer_boat_number' => 3,
                            'racer_course_number' => 3,
                            'racer_start_timing' => 0.31,
                            'racer_place_number' => 5,
                            'racer_number' => 3471,
                            'racer_name' => '赤峰 和也',
                        ],
                        4 => [
                            'racer_boat_number' => 4,
                            'racer_course_number' => 4,
                            'racer_start_timing' => 0.31,
                            'racer_place_number' => 4,
                            'racer_number' => 4574,
                            'racer_name' => '東 潤樹',
                        ],
                        5 => [
                            'racer_boat_number' => 5,
                            'racer_course_number' => 5,
                            'racer_start_timing' => 0.23,
                            'racer_place_number' => 3,
                            'racer_number' => 3800,
                            'racer_name' => '牧 宏次',
                        ],
                        6 => [
                            'racer_boat_number' => 6,
                            'racer_course_number' => 6,
                            'racer_start_timing' => 0.24,
                            'racer_place_number' => 6,
                            'racer_number' => 4924,
                            'racer_name' => '中北 涼',
                        ],
                    ],
                    'payouts' => [
                        'trifecta' => [
                            [
                                'combination' => '1-2-5',
                                'amount' => 460,
                            ],
                        ],
                        'trio' => [
                            [
                                'combination' => '1=2=5',
                                'amount' => 320,
                            ],
                        ],
                        'exacta' => [
                            [
                                'combination' => '1-2',
                                'amount' => 180,
                            ],
                        ],
                        'quinella' => [
                            [
                                'combination' => '1=2',
                                'amount' => 150,
                            ],
                        ],
                        'quinella_place' => [
                            [
                                'combination' => '1=2',
                                'amount' => 110,
                            ],
                            [
                                'combination' => '1=5',
                                'amount' => 240,
                            ],
                            [
                                'combination' => '2=5',
                                'amount' => 280,
                            ],
                        ],
                        'win' => [
                            [
                                'combination' => '1',
                                'amount' => 100,
                            ],
                        ],
                        'place' => [
                            [
                                'combination' => '1',
                                'amount' => 100,
                            ],
                            [
                                'combination' => '2',
                                'amount' => 150,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    'date' => '2019-10-14',
                    'stadium_number' => 2,
                    'number' => 1,
                    'wind_speed' => null,
                    'wind_direction_number' => null,
                    'wave_height' => null,
                    'weather_number' => null,
                    'air_temperature' => null,
                    'water_temperature' => null,
                    'technique_number' => null,
                    'boats' => [
                        1 => [
                            'racer_boat_number' => 1,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                        2 => [
                            'racer_boat_number' => 2,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                        3 => [
                            'racer_boat_number' => 3,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                        4 => [
                            'racer_boat_number' => 4,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                        5 => [
                            'racer_boat_number' => 5,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                        6 => [
                            'racer_boat_number' => 6,
                            'racer_course_number' => null,
                            'racer_start_timing' => null,
                            'racer_place_number' => null,
                            'racer_number' => null,
                            'racer_name' => null,
                        ],
                    ],
                    'payouts' => [
                        'trifecta' => [],
                        'trio' => [],
                        'exacta' => [],
                        'quinella' => [],
                        'quinella_place' => [],
                        'win' => [],
                        'place' => [],
                    ],
                ],
            ],
        ];
    }
}
