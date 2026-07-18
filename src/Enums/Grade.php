<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Grade: int
{
    case SG = 1;
    case G1 = 2;
    case G2 = 3;
    case G3 = 4;
    case IPPAN = 5;
    case PG1 = 6;

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
