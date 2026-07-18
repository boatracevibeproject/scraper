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
final class PreviewScraperDataProvider
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
                    'wind_speed_source' => '7m',
                    'wind_speed' => 7,
                    'wind_direction_number_source' => '南西',
                    'wind_direction_number' => 11,
                    'wave_height_source' => '6cm',
                    'wave_height' => 6,
                    'weather_number_source' => '曇り',
                    'weather_number' => 2,
                    'air_temperature_source' => '13.0℃',
                    'air_temperature' => 13.0,
                    'water_temperature_source' => '14.0℃',
                    'water_temperature' => 14.0,
                    'racers' => [
                        1 => [
                            'entry_number' => 1,
                            'course_number' => 1,
                            'start_timing_source' => '.15',
                            'start_timing' => 0.15,
                            'weight_source' => '54.0kg',
                            'weight' => 54.0,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.86',
                            'exhibition_time' => 6.86,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
                        ],
                        2 => [
                            'entry_number' => 2,
                            'course_number' => 2,
                            'start_timing_source' => '.22',
                            'start_timing' => 0.22,
                            'weight_source' => '54.2kg',
                            'weight' => 54.2,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.89',
                            'exhibition_time' => 6.89,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
                        ],
                        3 => [
                            'entry_number' => 3,
                            'course_number' => 3,
                            'start_timing_source' => '.19',
                            'start_timing' => 0.19,
                            'weight_source' => '52.6kg',
                            'weight' => 52.6,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.88',
                            'exhibition_time' => 6.88,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
                        ],
                        4 => [
                            'entry_number' => 4,
                            'course_number' => 4,
                            'start_timing_source' => '.18',
                            'start_timing' => 0.18,
                            'weight_source' => '51.2kg',
                            'weight' => 51.2,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.80',
                            'exhibition_time' => 6.8,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
                        ],
                        5 => [
                            'entry_number' => 5,
                            'course_number' => 5,
                            'start_timing_source' => '.05',
                            'start_timing' => 0.05,
                            'weight_source' => '51.6kg',
                            'weight' => 51.6,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.81',
                            'exhibition_time' => 6.81,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
                        ],
                        6 => [
                            'entry_number' => 6,
                            'course_number' => 6,
                            'start_timing_source' => '.11',
                            'start_timing' => 0.11,
                            'weight_source' => '47.5kg',
                            'weight' => 47.5,
                            'weight_adjustment_source' => '0.0',
                            'weight_adjustment' => 0.0,
                            'exhibition_time_source' => '6.76',
                            'exhibition_time' => 6.76,
                            'tilt_adjustment_source' => '-0.5',
                            'tilt_adjustment' => -0.5,
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
                    'racers' => array_combine(
                        range(1, 6),
                        array_map(fn(int $entryNumber): array => [
                            'entry_number' => $entryNumber,
                            'course_number' => null,
                            'start_timing_source' => null,
                            'start_timing' => null,
                            'weight_source' => null,
                            'weight' => null,
                            'weight_adjustment_source' => null,
                            'weight_adjustment' => null,
                            'exhibition_time_source' => null,
                            'exhibition_time' => null,
                            'tilt_adjustment_source' => null,
                            'tilt_adjustment' => null,
                        ], range(1, 6)),
                    ),
                ],
            ],
        ];
    }
}
