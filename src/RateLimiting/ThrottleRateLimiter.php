<?php

declare(strict_types=1);

namespace BVP\Scraper\RateLimiting;

use BVP\Scraper\Converters\Converter;
use InvalidArgumentException;

/**
 * Elapsed-time based throttle, scoped to the instance it is constructed on.
 *
 * Unlike a process-wide static throttle, two instances (e.g. one per proxy,
 * one per worker) never share `$lastCallAt`, so they cannot delay or starve
 * each other even when running in the same PHP process.
 *
 * @author shimomo
 */
final class ThrottleRateLimiter implements RateLimiterInterface
{
    /**
     * @var float
     */
    private const float DEFAULT_MIN_CALL_INTERVAL_SECONDS = 3.0;

    /**
     * @var float
     */
    private float $minCallIntervalSeconds;

    /**
     * @var ?float
     */
    private ?float $lastCallAt = null;

    /**
     * @param float $minCallIntervalSeconds
     */
    public function __construct(float $minCallIntervalSeconds = self::DEFAULT_MIN_CALL_INTERVAL_SECONDS)
    {
        if ($minCallIntervalSeconds < 0.0) {
            throw new InvalidArgumentException('$minCallIntervalSeconds must be 0 or greater.');
        }

        $this->minCallIntervalSeconds = $minCallIntervalSeconds;
    }

    /**
     * @return float
     */
    public function getMinCallIntervalSeconds(): float
    {
        return $this->minCallIntervalSeconds;
    }

    /**
     * @return void
     */
    #[\Override]
    public function throttle(): void
    {
        if ($this->lastCallAt !== null) {
            $elapsedSeconds = microtime(true) - $this->lastCallAt;
            $remainingSeconds = $this->minCallIntervalSeconds - $elapsedSeconds;

            if ($remainingSeconds > 0) {
                usleep(Converter::toIntStrict($remainingSeconds * 1_000_000.0));
            }
        }

        $this->lastCallAt = microtime(true);
    }
}
