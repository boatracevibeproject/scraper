<?php

declare(strict_types=1);

namespace BVP\Scraper\Parsers;

use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Enums\Place;
use BVP\Scraper\Enums\Technique;
use BVP\Scraper\Enums\Weather;
use BVP\Scraper\Enums\WindDirection;

/**
 * @author shimomo
 */
final class ResultParser
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WIND_SPEED_KEYS = [
        'wind_speed_source',
        'wind_speed',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WIND_DIRECTION_NUMBER_KEYS = [
        'wind_direction_number_source',
        'wind_direction_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WAVE_HEIGHT_KEYS = [
        'wave_height_source',
        'wave_height',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WEATHER_KEYS = [
        'weather_number_source',
        'weather_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array AIR_TEMPERATURE_KEYS = [
        'air_temperature_source',
        'air_temperature',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WATER_TEMPERATURE_KEYS = [
        'water_temperature_source',
        'water_temperature',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array TECHNIQUE_KEYS = [
        'technique_number_source',
        'technique_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array START_TIMING_KEYS = [
        'start_timing_source',
        'start_timing',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array PLACE_KEYS = [
        'place_number_source',
        'place_number',
    ];

    /**
     * @param ?string $value
     * @return array{
     *     wind_speed_source: ?string,
     *     wind_speed: ?int,
     * }
     */
    public static function parseWindSpeed(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WIND_SPEED_KEYS, null);
        }

        return array_combine(self::WIND_SPEED_KEYS, [
            Converter::toString($value),
            Converter::toInt($value),
        ]);
    }

    /**
     * $value is the digit already extracted from the `is-windN` CSS class by
     * WindDirectionFilter, so unlike the other fields here it is the page's
     * raw form — the "_source" field instead holds the human-readable name
     * looked up from that number via the WindDirection enum.
     *
     * @param ?string $value
     * @return array{
     *     wind_direction_number_source: ?string,
     *     wind_direction_number: ?int,
     * }
     */
    public static function parseWindDirection(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WIND_DIRECTION_NUMBER_KEYS, null);
        }

        return array_combine(self::WIND_DIRECTION_NUMBER_KEYS, [
            Converter::toString(
                Converter::toEnumOrNull(fn() => WindDirection::fromValue(Converter::toIntStrict($value)))?->name()
            ),
            Converter::toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     wave_height_source: ?string,
     *     wave_height: ?int,
     * }
     */
    public static function parseWaveHeight(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WAVE_HEIGHT_KEYS, null);
        }

        return array_combine(self::WAVE_HEIGHT_KEYS, [
            Converter::toString($value),
            Converter::toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     weather_number_source: ?string,
     *     weather_number: ?int,
     * }
     */
    public static function parseWeather(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WEATHER_KEYS, null);
        }

        return array_combine(self::WEATHER_KEYS, [
            Converter::toString($value),
            Converter::toInt(Converter::toEnumOrNull(fn() => Weather::fromName($value))?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     air_temperature_source: ?string,
     *     air_temperature: ?float,
     * }
     */
    public static function parseAirTemperature(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::AIR_TEMPERATURE_KEYS, null);
        }

        return array_combine(self::AIR_TEMPERATURE_KEYS, [
            Converter::toString($value),
            Converter::toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     water_temperature_source: ?string,
     *     water_temperature: ?float,
     * }
     */
    public static function parseWaterTemperature(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WATER_TEMPERATURE_KEYS, null);
        }

        return array_combine(self::WATER_TEMPERATURE_KEYS, [
            Converter::toString($value),
            Converter::toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     technique_number_source: ?string,
     *     technique_number: ?int,
     * }
     */
    public static function parseTechnique(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::TECHNIQUE_KEYS, null);
        }

        return array_combine(self::TECHNIQUE_KEYS, [
            Converter::toString($value),
            Converter::toInt(Converter::toEnumOrNull(fn() => Technique::fromName($value))?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     start_timing_source: ?string,
     *     start_timing: ?float,
     * }
     */
    public static function parseStartTiming(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::START_TIMING_KEYS, null);
        }

        if (!preg_match('/(L|F\.\d{2}|0?\.\d{2})/u', $value)) {
            return array_combine(self::START_TIMING_KEYS, [
                Converter::toString($value),
                Converter::toNull($value),
            ]);
        }

        $values = self::splitAndTrim($value, ' ');

        return array_combine(self::START_TIMING_KEYS, [
            Converter::toString(array_shift($values)),
            match (substr($value, 0, 1)) {
                'L' => Converter::toNull($value),
                'F' => Converter::toFloat('-0' . ltrim($value, 'F')),
                default => Converter::toFloat('0' . $value),
            },
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     place_number_source: ?string,
     *     place_number: ?int,
     * }
     */
    public static function parsePlace(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::PLACE_KEYS, null);
        }

        return array_combine(self::PLACE_KEYS, [
            Converter::toString($value),
            Converter::toInt(Converter::toEnumOrNull(fn() => Place::fromShortName($value))?->value),
        ]);
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $delimiter
     * @return list<?string>
     */
    private static function splitAndTrim(string $value, string $delimiter = '/'): array
    {
        return array_map(fn($v) => Converter::trim($v), explode($delimiter, $value));
    }
}
