<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Retry;

use BVP\Scraper\Retry\RetryPolicy;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @author shimomo
 */
final class RetryPolicyTest extends TestCase
{
    public function testReturnsResultOnFirstSuccess(): void
    {
        $retryPolicy = new RetryPolicy(maxAttempts: 3, retryDelaySeconds: 0);

        $result = $retryPolicy->run(fn(): string => 'ok');

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        $this->assertSame('ok', $result);
    }

    public function testRetriesOnRuntimeExceptionUntilItSucceeds(): void
    {
        $retryPolicy = new RetryPolicy(maxAttempts: 3, retryDelaySeconds: 0);

        $attempts = 0;
        $result = $retryPolicy->run(function () use (&$attempts): string {
            $attempts++;

            if ($attempts < 2) {
                throw new RuntimeException('temporary failure');
            }

            return 'ok';
        });

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        $this->assertSame('ok', $result);
        $this->assertSame(2, $attempts);
    }

    public function testRethrowsOnceAttemptsAreExhausted(): void
    {
        $retryPolicy = new RetryPolicy(maxAttempts: 2, retryDelaySeconds: 0);

        $attempts = 0;

        $this->expectException(RuntimeException::class);

        try {
            $retryPolicy->run(function () use (&$attempts): never {
                $attempts++;

                throw new RuntimeException('persistent failure');
            });
        } finally {
            $this->assertSame(2, $attempts);
        }
    }

    public function testDoesNotRetryOnNonRuntimeException(): void
    {
        $retryPolicy = new RetryPolicy(maxAttempts: 3, retryDelaySeconds: 0);

        $attempts = 0;

        $this->expectException(LogicException::class);

        try {
            $retryPolicy->run(function () use (&$attempts): never {
                $attempts++;

                throw new LogicException('not retryable');
            });
        } finally {
            $this->assertSame(1, $attempts);
        }
    }

    public function testRejectsInvalidMaxAttempts(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RetryPolicy(maxAttempts: 0);
    }

    public function testRejectsInvalidRetryDelay(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RetryPolicy(retryDelaySeconds: -1);
    }
}
