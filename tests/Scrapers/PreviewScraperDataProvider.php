<?php

declare(strict_types=1);

namespace BVP\BoatraceScraper\Tests\Scrapers;

use Carbon\CarbonImmutable as Carbon;

/**
 * @author shimomo
 */
final class PreviewScraperDataProvider
{
    /**
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
            [
                'arguments' => [Carbon::parse('2019-10-14'), 2, 1],
                'expected' => [
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
        ];
    }
}
