<?php

declare(strict_types=1);

namespace BVP\Scraper\Caching;

use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;

/**
 * Races on boatrace.jp are finalized same-day and are revised again only in
 * rare cases (e.g. a post-hoc official correction), so anything strictly
 * before "today" (in $timezone) is treated as immutable and cacheable
 * forever; today/future dates are never cached, since programs, previews,
 * and odds for them can still change between calls. Callers who know a
 * specific past race was corrected can pass `forceRefresh: true` to
 * {@see \BVP\Scraper\Scraper}'s scrape*() methods to bypass and overwrite a
 * stale cache entry.
 *
 * @author shimomo
 */
final class DateBasedCachePolicy implements CachePolicyInterface
{
    /**
     * @param non-empty-string $timezone
     */
    public function __construct(private readonly string $timezone = 'Asia/Tokyo')
    {
        //
    }

    /**
     * @param non-empty-string $type
     * @param \Carbon\CarbonInterface $date
     * @return bool
     */
    #[\Override]
    public function isCacheable(string $type, CarbonInterface $date): bool
    {
        $today = Carbon::now($this->timezone)->format('Y-m-d');

        return $date->format('Y-m-d') < $today;
    }
}
