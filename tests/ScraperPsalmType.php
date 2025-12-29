<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests;

/**
 * @psalm-type RaceDate = \Carbon\CarbonInterface
 * @psalm-type RaceStadiumNumber = int<1, 24>
 * @psalm-type RaceNumber = int<1, 12>
 * @psalm-type RaceArguments = array{RaceDate, RaceStadiumNumber, RaceNumber}
 * @psalm-type RaceExpected = non-empty-array<non-empty-string, mixed>
 * @psalm-type RaceExpectedByRace = non-empty-array<RaceNumber, RaceExpected>
 * @psalm-type RaceExpectedByStadium = non-empty-array<RaceStadiumNumber, RaceExpectedByRace>
 * @psalm-internal tests
 *
 * @author shimomo
 */
final class ScraperPsalmType
{
    //
}
