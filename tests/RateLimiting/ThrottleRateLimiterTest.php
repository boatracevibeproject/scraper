<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\RateLimiting;

use BVP\Scraper\RateLimiting\ThrottleRateLimiter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ThrottleRateLimiterTest extends TestCase
{
    public function testFirstCallDoesNotBlock(): void
    {
        $rateLimiter = new ThrottleRateLimiter(1.0);

        $startedAt = microtime(true);
        $rateLimiter->throttle();
        $elapsedSeconds = microtime(true) - $startedAt;

        $this->assertLessThan(0.1, $elapsedSeconds);
    }

    public function testSecondCallWithinIntervalBlocksForRemainingTime(): void
    {
        $rateLimiter = new ThrottleRateLimiter(1.0);

        $rateLimiter->throttle();

        $startedAt = microtime(true);
        $rateLimiter->throttle();
        $elapsedSeconds = microtime(true) - $startedAt;

        $this->assertGreaterThanOrEqual(0.95, $elapsedSeconds);
    }

    public function testIndependentInstancesDoNotShareState(): void
    {
        $slow = new ThrottleRateLimiter(1.5);
        $fast = new ThrottleRateLimiter(1.0);

        $slow->throttle();

        $startedAt = microtime(true);
        $fast->throttle();
        $elapsedSeconds = microtime(true) - $startedAt;

        $this->assertLessThan(0.1, $elapsedSeconds);
    }

    public function testGetMinCallIntervalSeconds(): void
    {
        $rateLimiter = new ThrottleRateLimiter(2.5);

        $this->assertSame(2.5, $rateLimiter->getMinCallIntervalSeconds());
    }

    public function testNegativeIntervalIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ThrottleRateLimiter(-1.0);
    }

    public function testIntervalBelowMinimumIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ThrottleRateLimiter(0.5);
    }
}
