<?php

declare(strict_types=1);

namespace BVP\Scraper\RateLimiting;

/**
 * @author shimomo
 */
interface RateLimiterInterface
{
    /**
     * Block until it is safe to issue the next request, then record the
     * moment it was issued. Implementations hold their pacing state on the
     * instance (not statically), so independent instances never share or
     * fight over a single rate budget.
     *
     * @return void
     */
    public function throttle(): void;
}
