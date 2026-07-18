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
                    'race_number' => 1,
                    'wind_speed_source' => '5m',
                    'wind_speed' => 5,
                    'wind_direction_number_source' => '南西',
                    'wind_direction_number' => 11,
                    'wave_height_source' => '4cm',
                    'wave_height' => 4,
                    'weather_number_source' => '雨',
                    'weather_number' => 3,
                    'air_temperature_source' => '13.0℃',
                    'air_temperature' => 13.0,
                    'water_temperature_source' => '14.0℃',
                    'water_temperature' => 14.0,
                    'technique_number_source' => '逃げ',
                    'technique_number' => 1,
                    'racers' => [
                        1 => [
                            'entry_number' => 1,
                            'course_number' => 1,
                            'start_timing_source' => '.25',
                            'start_timing' => 0.25,
                            'place_number_source' => '1',
                            'place_number' => 1,
                            'number_source' => '3833',
                            'number' => 3833,
                            'name' => '中辻 博訓',
                        ],
                        2 => [
                            'entry_number' => 2,
                            'course_number' => 2,
                            'start_timing_source' => '.28',
                            'start_timing' => 0.28,
                            'place_number_source' => '2',
                            'place_number' => 2,
                            'number_source' => '3773',
                            'number' => 3773,
                            'name' => '津留 浩一郎',
                        ],
                        3 => [
                            'entry_number' => 3,
                            'course_number' => 3,
                            'start_timing_source' => '.31',
                            'start_timing' => 0.31,
                            'place_number_source' => '5',
                            'place_number' => 5,
                            'number_source' => '3471',
                            'number' => 3471,
                            'name' => '赤峰 和也',
                        ],
                        4 => [
                            'entry_number' => 4,
                            'course_number' => 4,
                            'start_timing_source' => '.31',
                            'start_timing' => 0.31,
                            'place_number_source' => '4',
                            'place_number' => 4,
                            'number_source' => '4574',
                            'number' => 4574,
                            'name' => '東 潤樹',
                        ],
                        5 => [
                            'entry_number' => 5,
                            'course_number' => 5,
                            'start_timing_source' => '.23',
                            'start_timing' => 0.23,
                            'place_number_source' => '3',
                            'place_number' => 3,
                            'number_source' => '3800',
                            'number' => 3800,
                            'name' => '牧 宏次',
                        ],
                        6 => [
                            'entry_number' => 6,
                            'course_number' => 6,
                            'start_timing_source' => '.24',
                            'start_timing' => 0.24,
                            'place_number_source' => '6',
                            'place_number' => 6,
                            'number_source' => '4924',
                            'number' => 4924,
                            'name' => '中北 涼',
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
                    'race_number' => 1,
                    'wind_speed_source' => null,
                    'wind_speed' => null,
                    'wind_direction_number_source' => null,
                    'wind_direction_number' => null,
                    'wave_height_source' => null,
                    'wave_height' => null,
                    'weather_number_source' => null,
                    'weather_number' => null,
                    'air_temperature_source' => null,
                    'air_temperature' => null,
                    'water_temperature_source' => null,
                    'water_temperature' => null,
                    'technique_number_source' => null,
                    'technique_number' => null,
                    'racers' => [
                        1 => [
                            'entry_number' => 1,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
                        ],
                        2 => [
                            'entry_number' => 2,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
                        ],
                        3 => [
                            'entry_number' => 3,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
                        ],
                        4 => [
                            'entry_number' => 4,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
                        ],
                        5 => [
                            'entry_number' => 5,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
                        ],
                        6 => [
                            'entry_number' => 6,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'place_number_source' => null,
                            'place_number' => null,
                            'number_source' => null,
                            'number' => null,
                            'name' => null,
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
