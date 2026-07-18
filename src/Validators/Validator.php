<?php

declare(strict_types=1);

namespace BVP\Scraper\Validators;

use ValueError;

/**
 * @author shimomo
 */
final class Validator
{
    /**
     * @param int $stadiumNumber
     * @return void
     * @throws \ValueError
     */
    public static function validateStadiumNumber(int $stadiumNumber): void
    {
        if ($stadiumNumber <= 0 || $stadiumNumber >= 25) {
            throw new ValueError(
                sprintf('$stadiumNumber must be between 1 and 24, %d given.', $stadiumNumber)
            );
        }
    }

    /**
     * @param int $raceNumber
     * @return void
     * @throws \ValueError
     */
    public static function validateRaceNumber(int $raceNumber): void
    {
        if ($raceNumber <= 0 || $raceNumber >= 13) {
            throw new ValueError(
                sprintf('$raceNumber must be between 1 and 12, %d given.', $raceNumber)
            );
        }
    }
}
