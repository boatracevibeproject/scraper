<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum WindDirection: int
{
    case 北 = 1;
    case 北北東 = 2;
    case 北東 = 3;
    case 東北東 = 4;
    case 東 = 5;
    case 東南東 = 6;
    case 南東 = 7;
    case 南南東 = 8;
    case 南 = 9;
    case 南南西 = 10;
    case 南西 = 11;
    case 西南西 = 12;
    case 西 = 13;
    case 西北西 = 14;
    case 北西 = 15;
    case 北北西 = 16;
    case 無風 = 17;

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

    /**
     * @param ?int $value
     * @return ?self
     * @throws \ValueError
     */
    public static function fromValue(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        $case = self::tryFrom($value);

        if ($case === null) {
            throw new ValueError(
                "{$value} is not a valid value for enum " . self::class
            );
        }

        return $case;
    }
}
