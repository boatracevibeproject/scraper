<?php

declare(strict_types=1);

namespace BVP\BoatraceScraper\Tests;

use Carbon\CarbonImmutable as Carbon;

/**
 * @author shimomo
 */
final class ScraperCoreDataProvider
{
    /**
     * @return array
     */
    public static function scrapeOddsesProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31'), 24, 1],
                'expected' => [
                    24 => [
                        1 => [
                            'race_date' => '2017-03-31',
                            'race_stadium_number' => 24,
                            'race_number' => 1,
                            'win_oddses' => [
                                1 => 1.0,
                                2 => 25.6,
                                3 => 33.5,
                                4 => 31.1,
                                5 => 31.1,
                                6 => 218.2,
                            ],
                            'place_oddses' => [
                                1 => [
                                    'lower_limit' => 1.0,
                                    'upper_limit' => 1.0,
                                ],
                                2 => [
                                    'lower_limit' => 1.5,
                                    'upper_limit' => 20.9,
                                ],
                                3 => [
                                    'lower_limit' => 2.4,
                                    'upper_limit' => 37.8,
                                ],
                                4 => [
                                    'lower_limit' => 2.1,
                                    'upper_limit' => 33.3,
                                ],
                                5 => [
                                    'lower_limit' => 2.1,
                                    'upper_limit' => 33.3,
                                ],
                                6 => [
                                    'lower_limit' => 5.1,
                                    'upper_limit' => 86.6,
                                ],
                            ],
                            'exacta_oddses' => [
                                1 => [
                                    2 => 1.8,
                                    3 => 7.7,
                                    4 => 3.8,
                                    5 => 6.0,
                                    6 => 48.5,
                                ],
                                2 => [
                                    1 => 21.1,
                                    3 => 269.3,
                                    4 => 95.0,
                                    5 => 126.7,
                                    6 => 403.9,
                                ],
                                3 => [
                                    1 => 124.2,
                                    2 => 380.2,
                                    4 => 430.9,
                                    5 => 359.0,
                                    6 => 403.9,
                                ],
                                4 => [
                                    1 => 47.1,
                                    2 => 109.5,
                                    3 => 269.3,
                                    5 => 69.5,
                                    6 => 359.0,
                                ],
                                5 => [
                                    1 => 30.6,
                                    2 => 78.8,
                                    3 => 239.3,
                                    4 => 75.1,
                                    6 => 258.5,
                                ],
                                6 => [
                                    1 => 215.4,
                                    2 => 497.1,
                                    3 => 1077.0,
                                    4 => 923.3,
                                    5 => 359.0,
                                ],
                            ],
                            'quinella_oddses' => [
                                1 => [
                                    2 => 1.5,
                                    3 => 9.0,
                                    4 => 3.5,
                                    5 => 5.8,
                                    6 => 49.8,
                                ],
                                2 => [
                                    3 => 84.3,
                                    4 => 52.2,
                                    5 => 64.5,
                                    6 => 274.1,
                                ],
                                3 => [
                                    4 => 156.6,
                                    5 => 156.6,
                                    6 => 274.1,
                                ],
                                4 => [
                                    5 => 52.2,
                                    6 => 274.1,
                                ],
                                5 => [
                                    6 => 109.6,
                                ],
                            ],
                            'quinella_place_oddses' => [
                                1 => [
                                    2 => [
                                        'lower_limit' => 1.0,
                                        'upper_limit' => 1.2,
                                    ],
                                    3 => [
                                        'lower_limit' => 1.8,
                                        'upper_limit' => 2.5,
                                    ],
                                    4 => [
                                        'lower_limit' => 1.6,
                                        'upper_limit' => 2.5,
                                    ],
                                    5 => [
                                        'lower_limit' => 2.4,
                                        'upper_limit' => 3.6,
                                    ],
                                    6 => [
                                        'lower_limit' => 7.9,
                                        'upper_limit' => 10.9,
                                    ],
                                ],
                                2 => [
                                    3 => [
                                        'lower_limit' => 2.9,
                                        'upper_limit' => 4.8,
                                    ],
                                    4 => [
                                        'lower_limit' => 2.0,
                                        'upper_limit' => 3.3,
                                    ],
                                    5 => [
                                        'lower_limit' => 2.8,
                                        'upper_limit' => 4.4,
                                    ],
                                    6 => [
                                        'lower_limit' => 12.5,
                                        'upper_limit' => 18.0,
                                    ],
                                ],
                                3 => [
                                    4 => [
                                        'lower_limit' => 11.7,
                                        'upper_limit' => 15.0,
                                    ],
                                    5 => [
                                        'lower_limit' => 8.8,
                                        'upper_limit' => 10.6,
                                    ],
                                    6 => [
                                        'lower_limit' => 44.9,
                                        'upper_limit' => 50.7,
                                    ],
                                ],
                                4 => [
                                    5 => [
                                        'lower_limit' => 5.0,
                                        'upper_limit' => 6.1,
                                    ],
                                    6 => [
                                        'lower_limit' => 25.7,
                                        'upper_limit' => 29.3,
                                    ],
                                ],
                                5 => [
                                    6 => [
                                        'lower_limit' => 23.7,
                                        'upper_limit' => 25.5,
                                    ],
                                ],
                            ],
                            'trifecta_oddses' => [
                                1 => [
                                    2 => [
                                        3 => 9.4,
                                        4 => 3.6,
                                        5 => 4.6,
                                        6 => 57.7,
                                    ],
                                    3 => [
                                        2 => 23.5,
                                        4 => 26.1,
                                        5 => 31.2,
                                        6 => 147.3,
                                    ],
                                    4 => [
                                        2 => 8.9,
                                        3 => 29.0,
                                        5 => 13.4,
                                        6 => 75.8,
                                    ],
                                    5 => [
                                        2 => 14.0,
                                        3 => 34.9,
                                        4 => 20.4,
                                        6 => 85.8,
                                    ],
                                    6 => [
                                        2 => 196.3,
                                        3 => 346.8,
                                        4 => 209.9,
                                        5 => 221.7,
                                    ],
                                ],
                                2 => [
                                    1 => [
                                        3 => 91.6,
                                        4 => 62.8,
                                        5 => 63.2,
                                        6 => 228.1,
                                    ],
                                    3 => [
                                        1 => 513.3,
                                        4 => 870.1,
                                        5 => 814.9,
                                        6 => 1555.0,
                                    ],
                                    4 => [
                                        1 => 209.1,
                                        3 => 862.8,
                                        5 => 338.8,
                                        6 => 1267.0,
                                    ],
                                    5 => [
                                        1 => 198.2,
                                        3 => 828.0,
                                        4 => 390.4,
                                        6 => 968.6,
                                    ],
                                    6 => [
                                        1 => 1555.0,
                                        3 => 2139.0,
                                        4 => 2387.0,
                                        5 => 1974.0,
                                    ],
                                ],
                                3 => [
                                    1 => [
                                        2 => 390.4,
                                        4 => 518.5,
                                        5 => 448.3,
                                        6 => 1166.0,
                                    ],
                                    2 => [
                                        1 => 772.0,
                                        4 => 1333.0,
                                        5 => 1509.0,
                                        6 => 2775.0,
                                    ],
                                    4 => [
                                        1 => 933.4,
                                        2 => 1299.0,
                                        5 => 1351.0,
                                        6 => 2184.0,
                                    ],
                                    5 => [
                                        1 => 908.6,
                                        2 => 1740.0,
                                        4 => 1866.0,
                                        6 => 2013.0,
                                    ],
                                    6 => [
                                        1 => 2852.0,
                                        2 => 2933.0,
                                        4 => 1937.0,
                                        5 => 2184.0,
                                    ],
                                ],
                                4 => [
                                    1 => [
                                        2 => 120.9,
                                        3 => 397.9,
                                        5 => 133.8,
                                        6 => 561.0,
                                    ],
                                    2 => [
                                        1 => 220.8,
                                        3 => 987.2,
                                        5 => 386.0,
                                        6 => 1466.0,
                                    ],
                                    3 => [
                                        1 => 870.1,
                                        2 => 1047.0,
                                        5 => 1180.0,
                                        6 => 1711.0,
                                    ],
                                    5 => [
                                        1 => 196.7,
                                        2 => 365.4,
                                        3 => 942.0,
                                        6 => 744.0,
                                    ],
                                    6 => [
                                        1 => 1047.0,
                                        2 => 1237.0,
                                        3 => 1833.0,
                                        5 => 1426.0,
                                    ],
                                ],
                                5 => [
                                    1 => [
                                        2 => 109.9,
                                        3 => 284.4,
                                        4 => 158.9,
                                        6 => 338.8,
                                    ],
                                    2 => [
                                        1 => 194.8,
                                        3 => 855.6,
                                        4 => 431.4,
                                        6 => 987.2,
                                    ],
                                    3 => [
                                        1 => 603.9,
                                        2 => 1222.0,
                                        4 => 1237.0,
                                        6 => 1466.0,
                                    ],
                                    4 => [
                                        1 => 247.4,
                                        2 => 482.0,
                                        3 => 1006.0,
                                        6 => 1037.0,
                                    ],
                                    6 => [
                                        1 => 693.7,
                                        2 => 1193.0,
                                        3 => 1267.0,
                                        4 => 1092.0,
                                    ],
                                ],
                                6 => [
                                    1 => [
                                        2 => 1406.0,
                                        3 => 2095.0,
                                        4 => 1509.0,
                                        5 => 1252.0,
                                    ],
                                    2 => [
                                        1 => 2095.0,
                                        3 => 3540.0,
                                        4 => 2933.0,
                                        5 => 2387.0,
                                    ],
                                    3 => [
                                        1 => 3312.0,
                                        2 => 4107.0,
                                        4 => 2444.0,
                                        5 => 3422.0,
                                    ],
                                    4 => [
                                        1 => 2013.0,
                                        2 => 2702.0,
                                        3 => 2333.0,
                                        5 => 2504.0,
                                    ],
                                    5 => [
                                        1 => 1351.0,
                                        2 => 2852.0,
                                        3 => 2852.0,
                                        4 => 2632.0,
                                    ],
                                ],
                            ],
                            'trio_oddses' => [
                                1 => [
                                    2 => [
                                        3 => 6.5,
                                        4 => 2.4,
                                        5 => 3.2,
                                        6 => 27.2,
                                    ],
                                    3 => [
                                        4 => 13.3,
                                        5 => 13.4,
                                        6 => 77.7,
                                    ],
                                    4 => [
                                        5 => 7.1,
                                        6 => 37.5,
                                    ],
                                    5 => [
                                        6 => 48.3,
                                    ],
                                ],
                                2 => [
                                    3 => [
                                        4 => 114.5,
                                        5 => 94.6,
                                        6 => 241.9,
                                    ],
                                    4 => [
                                        5 => 34.5,
                                        6 => 120.9,
                                    ],
                                    5 => [
                                        6 => 311.0,
                                    ],
                                ],
                                3 => [
                                    4 => [
                                        5 => 136.0,
                                        6 => 155.5,
                                    ],
                                    5 => [
                                        6 => 197.9,
                                    ],
                                ],
                                4 => [
                                    5 => [
                                        6 => 167.4,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    2 => [
                        1 => [
                            'race_date' => '2019-10-14',
                            'race_stadium_number' => 2,
                            'race_number' => 1,
                            'win_oddses' => [
                                1 => null,
                                2 => null,
                                3 => null,
                                4 => null,
                                5 => null,
                                6 => null,
                            ],
                            'place_oddses' => [
                                1 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                                2 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                                3 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                                4 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                                5 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                                6 => [
                                    'lower_limit' => null,
                                    'upper_limit' => null,
                                ],
                            ],
                            'exacta_oddses' => [
                                1 => [
                                    2 => null,
                                    3 => null,
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                2 => [
                                    1 => null,
                                    3 => null,
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                3 => [
                                    1 => null,
                                    2 => null,
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                4 => [
                                    1 => null,
                                    2 => null,
                                    3 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                5 => [
                                    1 => null,
                                    2 => null,
                                    3 => null,
                                    4 => null,
                                    6 => null,
                                ],
                                6 => [
                                    1 => null,
                                    2 => null,
                                    3 => null,
                                    4 => null,
                                    5 => null,
                                ],
                            ],
                            'quinella_oddses' => [
                                1 => [
                                    2 => null,
                                    3 => null,
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                2 => [
                                    3 => null,
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                3 => [
                                    4 => null,
                                    5 => null,
                                    6 => null,
                                ],
                                4 => [
                                    5 => null,
                                    6 => null,
                                ],
                                5 => [
                                    6 => null,
                                ],
                            ],
                            'quinella_place_oddses' => [
                                1 => [
                                    2 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    3 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    4 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    5 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    6 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                ],
                                2 => [
                                    3 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    4 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    5 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    6 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                ],
                                3 => [
                                    4 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    5 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    6 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                ],
                                4 => [
                                    5 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                    6 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                ],
                                5 => [
                                    6 => [
                                        'lower_limit' => null,
                                        'upper_limit' => null,
                                    ],
                                ],
                            ],
                            'trifecta_oddses' => [
                                1 => [
                                    2 => [
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    3 => [
                                        2 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        2 => null,
                                        3 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    6 => [
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                ],
                                2 => [
                                    1 => [
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    3 => [
                                        1 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        1 => null,
                                        3 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        1 => null,
                                        3 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    6 => [
                                        1 => null,
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                ],
                                3 => [
                                    1 => [
                                        2 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    2 => [
                                        1 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        1 => null,
                                        2 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        1 => null,
                                        2 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    6 => [
                                        1 => null,
                                        2 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                ],
                                4 => [
                                    1 => [
                                        2 => null,
                                        3 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    2 => [
                                        1 => null,
                                        3 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    3 => [
                                        1 => null,
                                        2 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        6 => null,
                                    ],
                                    6 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        5 => null,
                                    ],
                                ],
                                5 => [
                                    1 => [
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    2 => [
                                        1 => null,
                                        3 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    3 => [
                                        1 => null,
                                        2 => null,
                                        4 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        6 => null,
                                    ],
                                    6 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                    ],
                                ],
                                6 => [
                                    1 => [
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                    2 => [
                                        1 => null,
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                    3 => [
                                        1 => null,
                                        2 => null,
                                        4 => null,
                                        5 => null,
                                    ],
                                    4 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        5 => null,
                                    ],
                                    5 => [
                                        1 => null,
                                        2 => null,
                                        3 => null,
                                        4 => null,
                                    ],
                                ],
                            ],
                            'trio_oddses' => [
                                1 => [
                                    2 => [
                                        3 => null,
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    3 => [
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        6 => null,
                                    ],
                                ],
                                2 => [
                                    3 => [
                                        4 => null,
                                        5 => null,
                                        6 => null,
                                    ],
                                    4 => [
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        6 => null,
                                    ],
                                ],
                                3 => [
                                    4 => [
                                        5 => null,
                                        6 => null,
                                    ],
                                    5 => [
                                        6 => null,
                                    ],
                                ],
                                4 => [
                                    5 => [
                                        6 => null,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function scrapePreviewsProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31'), 24, 1],
                'expected' => [
                    24 => [
                        1 => [
                            'race_date' => '2017-03-31',
                            'race_stadium_number' => 24,
                            'race_number' => 1,
                            'race_wind' => 7,
                            'race_wind_direction_number' => 11,
                            'race_wave' => 6,
                            'race_weather_number' => 2,
                            'race_temperature' => 13.0,
                            'race_water_temperature' => 14.0,
                            'boats' => [
                                1 => [
                                    'racer_boat_number' => 1,
                                    'racer_course_number' => 1,
                                    'racer_start_timing' => 0.15,
                                    'racer_weight' => 54.0,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.86,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                                2 => [
                                    'racer_boat_number' => 2,
                                    'racer_course_number' => 2,
                                    'racer_start_timing' => 0.22,
                                    'racer_weight' => 54.2,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.89,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                                3 => [
                                    'racer_boat_number' => 3,
                                    'racer_course_number' => 3,
                                    'racer_start_timing' => 0.19,
                                    'racer_weight' => 52.6,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.88,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                                4 => [
                                    'racer_boat_number' => 4,
                                    'racer_course_number' => 4,
                                    'racer_start_timing' => 0.18,
                                    'racer_weight' => 51.2,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.80,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                                5 => [
                                    'racer_boat_number' => 5,
                                    'racer_course_number' => 5,
                                    'racer_start_timing' => 0.05,
                                    'racer_weight' => 51.6,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.81,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                                6 => [
                                    'racer_boat_number' => 6,
                                    'racer_course_number' => 6,
                                    'racer_start_timing' => 0.11,
                                    'racer_weight' => 47.5,
                                    'racer_weight_adjustment' => 0.0,
                                    'racer_exhibition_time' => 6.76,
                                    'racer_tilt_adjustment' => -0.5,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    2 => [
                        1 => [
                            'race_date' => '2019-10-14',
                            'race_stadium_number' => 2,
                            'race_number' => 1,
                            'race_wind' => null,
                            'race_wind_direction_number' => null,
                            'race_wave' => null,
                            'race_weather_number' => null,
                            'race_temperature' => null,
                            'race_water_temperature' => null,
                            'boats' => [
                                1 => [
                                    'racer_boat_number' => 1,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                                2 => [
                                    'racer_boat_number' => 2,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                                3 => [
                                    'racer_boat_number' => 3,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                                4 => [
                                    'racer_boat_number' => 4,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                                5 => [
                                    'racer_boat_number' => 5,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                                6 => [
                                    'racer_boat_number' => 6,
                                    'racer_course_number' => null,
                                    'racer_start_timing' => null,
                                    'racer_weight' => null,
                                    'racer_weight_adjustment' => null,
                                    'racer_exhibition_time' => null,
                                    'racer_tilt_adjustment' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function scrapeProgramsProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31'), 24, 1],
                'expected' => [
                    24 => [
                        1 => [
                            'race_date' => '2017-03-31',
                            'race_stadium_number' => 24,
                            'race_number' => 1,
                            'race_closed_at' => '2017-03-31 12:00:00',
                            'race_title' => 'おおむら桜祭り競走',
                            'race_subtitle' => 'めざまし戦一般',
                            'race_distance' => 1800,
                            'boats' => [
                                1 => [
                                    'racer_boat_number' => 1,
                                    'racer_name' => '中辻 博訓',
                                    'racer_number' => 3833,
                                    'racer_class_number' => 1,
                                    'racer_branch_number' => 18,
                                    'racer_birthplace_number' => 18,
                                    'racer_age' => 42,
                                    'racer_weight' => 54.0,
                                    'racer_flying_count' => 1,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.13,
                                    'racer_national_top_1_percent' => 6.45,
                                    'racer_national_top_2_percent' => 45.36,
                                    'racer_national_top_3_percent' => 65.98,
                                    'racer_local_top_1_percent' => 7.78,
                                    'racer_local_top_2_percent' => 55.56,
                                    'racer_local_top_3_percent' => 77.78,
                                    'racer_assigned_motor_number' => 66,
                                    'racer_assigned_motor_top_2_percent' => 88.89,
                                    'racer_assigned_motor_top_3_percent' => 100.00,
                                    'racer_assigned_boat_number' => 71,
                                    'racer_assigned_boat_top_2_percent' => 37.14,
                                    'racer_assigned_boat_top_3_percent' => 55.00,
                                ],
                                2 => [
                                    'racer_boat_number' => 2,
                                    'racer_name' => '津留 浩一郎',
                                    'racer_number' => 3773,
                                    'racer_class_number' => 3,
                                    'racer_branch_number' => 42,
                                    'racer_birthplace_number' => 42,
                                    'racer_age' => 42,
                                    'racer_weight' => 54.2,
                                    'racer_flying_count' => 0,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.18,
                                    'racer_national_top_1_percent' => 5.70,
                                    'racer_national_top_2_percent' => 35.85,
                                    'racer_national_top_3_percent' => 61.32,
                                    'racer_local_top_1_percent' => 6.40,
                                    'racer_local_top_2_percent' => 52.48,
                                    'racer_local_top_3_percent' => 65.35,
                                    'racer_assigned_motor_number' => 43,
                                    'racer_assigned_motor_top_2_percent' => 25.00,
                                    'racer_assigned_motor_top_3_percent' => 37.50,
                                    'racer_assigned_boat_number' => 41,
                                    'racer_assigned_boat_top_2_percent' => 28.87,
                                    'racer_assigned_boat_top_3_percent' => 40.14,
                                ],
                                3 => [
                                    'racer_boat_number' => 3,
                                    'racer_name' => '赤峰 和也',
                                    'racer_number' => 3471,
                                    'racer_class_number' => 3,
                                    'racer_branch_number' => 41,
                                    'racer_birthplace_number' => 41,
                                    'racer_age' => 47,
                                    'racer_weight' => 52.6,
                                    'racer_flying_count' => 0,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.21,
                                    'racer_national_top_1_percent' => 4.13,
                                    'racer_national_top_2_percent' => 16.50,
                                    'racer_national_top_3_percent' => 33.01,
                                    'racer_local_top_1_percent' => 3.95,
                                    'racer_local_top_2_percent' => 14.29,
                                    'racer_local_top_3_percent' => 33.33,
                                    'racer_assigned_motor_number' => 59,
                                    'racer_assigned_motor_top_2_percent' => 33.33,
                                    'racer_assigned_motor_top_3_percent' => 44.44,
                                    'racer_assigned_boat_number' => 67,
                                    'racer_assigned_boat_top_2_percent' => 30.71,
                                    'racer_assigned_boat_top_3_percent' => 50.71,
                                ],
                                4 => [
                                    'racer_boat_number' => 4,
                                    'racer_name' => '東 潤樹',
                                    'racer_number' => 4574,
                                    'racer_class_number' => 3,
                                    'racer_branch_number' => 34,
                                    'racer_birthplace_number' => 38,
                                    'racer_age' => 28,
                                    'racer_weight' => 51.2,
                                    'racer_flying_count' => 1,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.17,
                                    'racer_national_top_1_percent' => 4.88,
                                    'racer_national_top_2_percent' => 33.78,
                                    'racer_national_top_3_percent' => 41.89,
                                    'racer_local_top_1_percent' => 4.17,
                                    'racer_local_top_2_percent' => 13.04,
                                    'racer_local_top_3_percent' => 30.43,
                                    'racer_assigned_motor_number' => 73,
                                    'racer_assigned_motor_top_2_percent' => 37.50,
                                    'racer_assigned_motor_top_3_percent' => 50.00,
                                    'racer_assigned_boat_number' => 34,
                                    'racer_assigned_boat_top_2_percent' => 27.86,
                                    'racer_assigned_boat_top_3_percent' => 42.14,
                                ],
                                5 => [
                                    'racer_boat_number' => 5,
                                    'racer_name' => '牧 宏次',
                                    'racer_number' => 3800,
                                    'racer_class_number' => 3,
                                    'racer_branch_number' => 13,
                                    'racer_birthplace_number' => 14,
                                    'racer_age' => 43,
                                    'racer_weight' => 51.6,
                                    'racer_flying_count' => 0,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.20,
                                    'racer_national_top_1_percent' => 5.05,
                                    'racer_national_top_2_percent' => 34.48,
                                    'racer_national_top_3_percent' => 45.98,
                                    'racer_local_top_1_percent' => 5.00,
                                    'racer_local_top_2_percent' => 37.50,
                                    'racer_local_top_3_percent' => 37.50,
                                    'racer_assigned_motor_number' => 52,
                                    'racer_assigned_motor_top_2_percent' => 0.00,
                                    'racer_assigned_motor_top_3_percent' => 0.00,
                                    'racer_assigned_boat_number' => 65,
                                    'racer_assigned_boat_top_2_percent' => 39.57,
                                    'racer_assigned_boat_top_3_percent' => 56.83,
                                ],
                                6 => [
                                    'racer_boat_number' => 6,
                                    'racer_name' => '中北 涼',
                                    'racer_number' => 4924,
                                    'racer_class_number' => 4,
                                    'racer_branch_number' => 42,
                                    'racer_birthplace_number' => 23,
                                    'racer_age' => 24,
                                    'racer_weight' => 47.5,
                                    'racer_flying_count' => 1,
                                    'racer_late_count' => 0,
                                    'racer_average_start_timing' => 0.18,
                                    'racer_national_top_1_percent' => 1.76,
                                    'racer_national_top_2_percent' => 1.96,
                                    'racer_national_top_3_percent' => 1.96,
                                    'racer_local_top_1_percent' => 0.00,
                                    'racer_local_top_2_percent' => 0.00,
                                    'racer_local_top_3_percent' => 0.00,
                                    'racer_assigned_motor_number' => 64,
                                    'racer_assigned_motor_top_2_percent' => 66.67,
                                    'racer_assigned_motor_top_3_percent' => 88.89,
                                    'racer_assigned_boat_number' => 46,
                                    'racer_assigned_boat_top_2_percent' => 24.82,
                                    'racer_assigned_boat_top_3_percent' => 42.55,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    2 => [
                        1 => [
                            'race_date' => '2019-10-14',
                            'race_stadium_number' => 2,
                            'race_number' => 1,
                            'race_closed_at' => null,
                            'race_title' => null,
                            'race_subtitle' => null,
                            'race_distance' => null,
                            'boats' => [
                                1 => [
                                    'racer_boat_number' => 1,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                                2 => [
                                    'racer_boat_number' => 2,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                                3 => [
                                    'racer_boat_number' => 3,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                                4 => [
                                    'racer_boat_number' => 4,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                                5 => [
                                    'racer_boat_number' => 5,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                                6 => [
                                    'racer_boat_number' => 6,
                                    'racer_name' => null,
                                    'racer_number' => null,
                                    'racer_class_number' => null,
                                    'racer_branch_number' => null,
                                    'racer_birthplace_number' => null,
                                    'racer_age' => null,
                                    'racer_weight' => null,
                                    'racer_flying_count' => null,
                                    'racer_late_count' => null,
                                    'racer_average_start_timing' => null,
                                    'racer_national_top_1_percent' => null,
                                    'racer_national_top_2_percent' => null,
                                    'racer_national_top_3_percent' => null,
                                    'racer_local_top_1_percent' => null,
                                    'racer_local_top_2_percent' => null,
                                    'racer_local_top_3_percent' => null,
                                    'racer_assigned_motor_number' => null,
                                    'racer_assigned_motor_top_2_percent' => null,
                                    'racer_assigned_motor_top_3_percent' => null,
                                    'racer_assigned_boat_number' => null,
                                    'racer_assigned_boat_top_2_percent' => null,
                                    'racer_assigned_boat_top_3_percent' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function scrapeResultsProvider(): array
    {
        return [
            [
                'arguments' => [Carbon::parse('2017-03-31'), 24, 1],
                'expected' => [
                    24 => [
                        1 => [
                            'race_date' => '2017-03-31',
                            'race_stadium_number' => 24,
                            'race_number' => 1,
                            'race_wind' => 5,
                            'race_wind_direction_number' => 11,
                            'race_wave' => 4,
                            'race_weather_number' => 3,
                            'race_temperature' => 13.0,
                            'race_water_temperature' => 14.0,
                            'race_technique_number' => 1,
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
                                        'payout' => 460,
                                    ],
                                ],
                                'trio' => [
                                    [
                                        'combination' => '1=2=5',
                                        'payout' => 320,
                                    ],
                                ],
                                'exacta' => [
                                    [
                                        'combination' => '1-2',
                                        'payout' => 180,
                                    ],
                                ],
                                'quinella' => [
                                    [
                                        'combination' => '1=2',
                                        'payout' => 150,
                                    ],
                                ],
                                'quinella_place' => [
                                    [
                                        'combination' => '1=2',
                                        'payout' => 110,
                                    ],
                                    [
                                        'combination' => '1=5',
                                        'payout' => 240,
                                    ],
                                    [
                                        'combination' => '2=5',
                                        'payout' => 280,
                                    ],
                                ],
                                'win' => [
                                    [
                                        'combination' => '1',
                                        'payout' => 100,
                                    ],
                                ],
                                'place' => [
                                    [
                                        'combination' => '1',
                                        'payout' => 100,
                                    ],
                                    [
                                        'combination' => '2',
                                        'payout' => 150,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    2 => [
                        1 => [
                            'race_date' => '2019-10-14',
                            'race_stadium_number' => 2,
                            'race_number' => 1,
                            'race_wind' => null,
                            'race_wind_direction_number' => null,
                            'race_wave' => null,
                            'race_weather_number' => null,
                            'race_temperature' => null,
                            'race_water_temperature' => null,
                            'race_technique_number' => null,
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
                ],
            ],
        ];
    }

    /**
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
