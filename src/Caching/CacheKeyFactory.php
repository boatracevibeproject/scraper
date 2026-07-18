<?php

declare(strict_types=1);

namespace BVP\Scraper\Caching;

use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
final class CacheKeyFactory
{
    /**
     * @param non-empty-string $type
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?non-empty-string $betType
     * @return non-empty-string
     */
    public static function make(
        string $type,
        CarbonInterface $date,
        int $stadiumNumber,
        int $raceNumber,
        ?string $betType = null,
    ): string {
        $segments = [$type, $date->format('Ymd'), (string) $stadiumNumber, (string) $raceNumber];

        if ($betType !== null) {
            $segments[] = $betType;
        }

        return implode('.', $segments);
    }

    /**
     * @param \Carbon\CarbonInterface $date
     * @return non-empty-string
     */
    public static function makeForStadium(CarbonInterface $date): string
    {
        return implode('.', ['stadium', $date->format('Ymd')]);
    }
}
