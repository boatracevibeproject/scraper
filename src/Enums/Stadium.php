<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Stadium: int
{
    case 桐生 = 1;
    case 戸田 = 2;
    case 江戸川 = 3;
    case 平和島 = 4;
    case 多摩川 = 5;
    case 浜名湖 = 6;
    case 蒲郡 = 7;
    case 常滑 = 8;
    case 津 = 9;
    case 三国 = 10;
    case びわこ = 11;
    case 住之江 = 12;
    case 尼崎 = 13;
    case 鳴門 = 14;
    case 丸亀 = 15;
    case 児島 = 16;
    case 宮島 = 17;
    case 徳山 = 18;
    case 下関 = 19;
    case 若松 = 20;
    case 芦屋 = 21;
    case 福岡 = 22;
    case 唐津 = 23;
    case 大村 = 24;

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
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
}
