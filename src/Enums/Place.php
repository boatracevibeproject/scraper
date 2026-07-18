<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Place: int
{
    case 一着 = 1;
    case 二着 = 2;
    case 三着 = 3;
    case 四着 = 4;
    case 五着 = 5;
    case 六着 = 6;
    case 妨害失格 = 7;
    case エンスト失格 = 8;
    case 転覆失格 = 9;
    case 落水失格 = 10;
    case 沈没失格 = 11;
    case 不完走失格 = 12;
    case 失格 = 13;
    case フライング欠場 = 14;
    case 出遅れ欠場 = 15;
    case 欠場 = 16;
    case その他 = 99;

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return match ($this) {
            self::一着 => '1着',
            self::二着 => '2着',
            self::三着 => '3着',
            self::四着 => '4着',
            self::五着 => '5着',
            self::六着 => '6着',
            self::妨害失格 => '妨害失格',
            self::エンスト失格 => 'エンスト失格',
            self::転覆失格 => '転覆失格',
            self::落水失格 => '落水失格',
            self::沈没失格 => '沈没失格',
            self::不完走失格 => '不完走失格',
            self::失格 => '失格',
            self::フライング欠場 => 'フライング欠場',
            self::出遅れ欠場 => '出遅れ欠場',
            self::欠場 => '欠場',
            self::その他 => '_',
        };
    }

    /**
     * @return non-empty-string
     */
    public function shortName(): string
    {
        return match ($this) {
            self::一着 => '1',
            self::二着 => '2',
            self::三着 => '3',
            self::四着 => '4',
            self::五着 => '5',
            self::六着 => '6',
            self::妨害失格 => '妨',
            self::エンスト失格 => 'エ',
            self::転覆失格 => '転',
            self::落水失格 => '落',
            self::沈没失格 => '沈',
            self::不完走失格 => '不',
            self::失格 => '失',
            self::フライング欠場 => 'F',
            self::出遅れ欠場 => 'L',
            self::欠場 => '欠',
            self::その他 => '_',
        };
    }

    /**
     * @param ?string $name
     * @return ?self
     * @throws \ValueError
     */
    public static function fromName(?string $name): ?self
    {
        if ($name === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->name() === $name) {
                return $case;
            }
        }

        throw new ValueError(
            "{$name} is not a valid name for enum " . self::class
        );
    }

    /**
     * @param ?string $shortName
     * @return ?self
     * @throws \ValueError
     */
    public static function fromShortName(?string $shortName): ?self
    {
        if ($shortName === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->shortName() === $shortName) {
                return $case;
            }
        }

        throw new ValueError(
            "{$shortName} is not a valid name for enum " . self::class
        );
    }

    /**
     * @return list<array{
     *     number: int,
     *     name: non-empty-string,
     *     short_name: non-empty-string,
     * }>
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'number' => $case->value,
            'name' => $case->name(),
            'short_name' => $case->shortName(),
        ], self::cases());
    }
}
