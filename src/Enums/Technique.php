<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Technique: int
{
    case 逃げ = 1;
    case 差し = 2;
    case まくり = 3;
    case まくり差し = 4;
    case 抜き = 5;
    case 恵まれ = 6;

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
