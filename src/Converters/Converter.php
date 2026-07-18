<?php

declare(strict_types=1);

namespace BVP\Scraper\Converters;

use ValueError;

/**
 * @author shimomo
 */
final class Converter
{
    /**
     * @param int|float|string|null $value
     * @return ?int
     */
    public static function toInt(int|float|string|null $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }

    /**
     * @param int|float|string $value
     * @return int
     */
    public static function toIntStrict(int|float|string $value): int
    {
        return (int) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    public static function toIntOrReturn(int|float|string|null $value): int|float|string|null
    {
        return is_numeric($value) ? (int) $value : $value;
    }

    /**
     * @param int|float|string|null $value
     * @return ?float
     */
    public static function toFloat(int|float|string|null $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }

    /**
     * @param int|float|string $value
     * @return float
     */
    public static function toFloatStrict(int|float|string $value): float
    {
        return (float) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return int|float|string|null
     */
    public static function toFloatOrReturn(int|float|string|null $value): int|float|string|null
    {
        return is_numeric($value) ? (float) $value : $value;
    }

    /**
     * @param int|float|string|null $value
     * @return ?string
     */
    public static function toString(int|float|string|null $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }

    /**
     * @param int|float|string $value
     * @return string
     */
    public static function toStringStrict(int|float|string $value): string
    {
        return (string) $value;
    }

    /**
     * @param int|float|string|null $value
     * @return null
     */
    public static function toNull(int|float|string|null $value): null
    {
        return null;
    }

    /**
     * @param ?string $value
     * @param string $mode
     * @return ?string
     */
    public static function toKana(?string $value, string $mode = 'KVas'): ?string
    {
        return $value !== null ? mb_convert_kana($value, $mode, 'UTF-8') : null;
    }

    /**
     * Multibyte-safe trim, including the full-width space (U+3000) boatrace.jp
     * markup frequently uses. Kept as our own helper (rather than PHP 8.4's
     * mb_trim()) because this library targets php: ^8.2.
     *
     * @param ?string $value
     * @return ?string
     */
    public static function trim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/^[\s\x{3000}]+|[\s\x{3000}]+$/u', '', $value) ?? $value;
    }

    /**
     * @param ?string $value
     * @return ?int
     */
    public static function toDayNumber(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $value = self::trim($value);
        $value = self::toKana($value);
        $value = self::toInt($value);

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function toCamelCase(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }

    /**
     * @template TValue
     * @param array<string, TValue> $array
     * @return array<string, TValue>
     */
    public static function toCamelCaseKeys(array $array): array
    {
        $response = [];

        foreach ($array as $key => $value) {
            $response[self::toCamelCase($key)] = $value;
        }

        return $response;
    }

    /**
     * @template T of \UnitEnum
     * @param callable(): ?T $resolver
     * @return ?T
     */
    public static function toEnumOrNull(callable $resolver): ?object
    {
        try {
            return $resolver();
        } catch (ValueError) {
            return null;
        }
    }
}
