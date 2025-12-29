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
final class OddsScraperDataProvider
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
                    'race_date' => '2017-03-31',
                    'race_stadium_number' => 24,
                    'race_number' => 1,
                    'win_odds' => [
                        1 => 1.0,
                        2 => 25.6,
                        3 => 33.5,
                        4 => 31.1,
                        5 => 31.1,
                        6 => 218.2,
                    ],
                    'place_odds' => [
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
                    'exacta_odds' => [
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
                    'quinella_odds' => [
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
                    'quinella_place_odds' => [
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
                    'trifecta_odds' => [
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
                    'trio_odds' => [
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
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
                    'race_date' => '2019-10-14',
                    'race_stadium_number' => 2,
                    'race_number' => 1,
                    'win_odds' => [
                        1 => null,
                        2 => null,
                        3 => null,
                        4 => null,
                        5 => null,
                        6 => null,
                    ],
                    'place_odds' => [
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
                    'exacta_odds' => [
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
                    'quinella_odds' => [
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
                    'quinella_place_odds' => [
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
                    'trifecta_odds' => [
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
                    'trio_odds' => [
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
        ];
    }
}
