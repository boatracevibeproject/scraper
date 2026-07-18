<?php

declare(strict_types=1);

namespace BVP\Scraper\Parsers;

use BVP\Scraper\Converters\Converter;

/**
 * Shared racer-identity parsing used by Program/Preview/Result scrapers.
 *
 * @author shimomo
 */
final class Parser
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array ENTRY_NUMBER_KEYS = [
        'entry_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NUMBER_KEYS = [
        'number_source',
        'number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NAME_KEYS = [
        'name',
    ];

    /**
     * Some racer names on boatrace.jp render without a space between the
     * family and given name (breaking the usual "family given" split used
     * by parseName()); this maps those specific known cases back to their
     * properly spaced form.
     *
     * @var array<non-empty-string, non-empty-string>
     */
    private const array UNSPACED_NAME_MAP = [
        '小神野紀代子' => '小神野 紀代子',
        '堀之内紀代子' => '堀之内 紀代子',
        '大久保信一郎' => '大久保 信一郎',
        'マイケル田代' => 'マイケル 田代',
        '安河内鈴之介' => '安河内 鈴之介',
    ];

    /**
     * @param ?string $value
     * @return array{
     *     entry_number: ?int,
     * }
     */
    public static function parseEntryNumber(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::ENTRY_NUMBER_KEYS, null);
        }

        return array_combine(self::ENTRY_NUMBER_KEYS, [
            Converter::toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     number_source: ?string,
     *     number: ?int,
     * }
     */
    public static function parseNumber(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NUMBER_KEYS, null);
        }

        return array_combine(self::NUMBER_KEYS, [
            Converter::toString($value),
            Converter::toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     name: ?string,
     * }
     */
    public static function parseName(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NAME_KEYS, null);
        }

        $pattern = '/([\p{L}\p{M}\p{N}]+)\s+([\p{L}\p{M}\p{N}]+)/u';
        if (preg_match($pattern, $value, $matches)) {
            return array_combine(self::NAME_KEYS, [
                Converter::toString($matches[1] . ' ' . $matches[2]),
            ]);
        }

        return array_combine(self::NAME_KEYS, [
            Converter::toString(self::UNSPACED_NAME_MAP[$value] ?? null),
        ]);
    }
}
