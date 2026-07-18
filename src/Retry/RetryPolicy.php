<?php

declare(strict_types=1);

namespace BVP\Scraper\Retry;

use InvalidArgumentException;
use RuntimeException;

/**
 * @author shimomo
 */
final class RetryPolicy
{
    /**
     * $maxAttempts/$retryDelaySeconds are validated at runtime (rather than
     * narrowed via psalm-only docblock types) because this constructor is a
     * public boundary that must reject bad input from any caller, not just
     * internal call sites.
     *
     * @param int $maxAttempts
     * @param int $retryDelaySeconds
     */
    public function __construct(
        private readonly int $maxAttempts = 3,
        private readonly int $retryDelaySeconds = 3,
    ) {
        if ($maxAttempts < 1) {
            throw new InvalidArgumentException('$maxAttempts must be 1 or greater.');
        }

        if ($retryDelaySeconds < 0) {
            throw new InvalidArgumentException('$retryDelaySeconds must be 0 or greater.');
        }
    }

    /**
     * Runs $callback, retrying on \RuntimeException up to $maxAttempts times
     * (with a sleep of $retryDelaySeconds between attempts). The last
     * exception is rethrown once attempts are exhausted.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function run(callable $callback): mixed
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                return $callback();
            } catch (RuntimeException $exception) {
                if ($attempt >= $this->maxAttempts) {
                    throw $exception;
                }

                sleep($this->retryDelaySeconds);
            }
        }
    }
}
