<?php

declare(strict_types=1);

namespace BVP\Scraper\Caching;

use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface CachePolicyInterface
{
    /**
     * Decides whether a scrape for $type on $date is eligible for caching at
     * all. Implementations typically key this off how likely the underlying
     * page is to still change (e.g. a finalized past race never changes; an
     * in-progress or future one does).
     *
     * @param non-empty-string $type
     * @param \Carbon\CarbonInterface $date
     * @return bool
     */
    public function isCacheable(string $type, CarbonInterface $date): bool;
}
