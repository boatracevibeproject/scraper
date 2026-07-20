<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests;

use BVP\Scraper\Caching\CacheFactory;
use BVP\Scraper\Caching\CacheKeyFactory;
use BVP\Scraper\Scraper;
use BVP\Scraper\Tests\Scrapers\ResultScraperDataProvider;
use Carbon\CarbonImmutable as Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the forceRefresh escape hatch used for the rare case where
 * boatrace.jp corrects an already-finalized past race: a normal call must
 * keep trusting the cache, while forceRefresh must bypass it, fetch live,
 * and overwrite the stale entry for subsequent normal calls.
 *
 * @author shimomo
 */
final class ForceRefreshTest extends TestCase
{
    public function testForceRefreshBypassesAndOverwritesTheCache(): void
    {
        $cacheDir = sys_get_temp_dir() . '/bvp-scraper-force-refresh-test-' . uniqid();
        $cache = CacheFactory::createDefault($cacheDir);
        $scraper = new Scraper(cache: $cache);

        [$date, $stadiumNumber, $raceNumber] = ResultScraperDataProvider::scrapeProvider()[0]['arguments'];
        $correctResult = ResultScraperDataProvider::scrapeProvider()[0]['expected'];

        $staleResult = ['this' => 'is a deliberately wrong pre-seeded value'];
        $cacheKey = CacheKeyFactory::make('result', Carbon::parse($date), $stadiumNumber, $raceNumber);
        $cache->set($cacheKey, $staleResult);

        $this->assertSame(
            $staleResult,
            $scraper->scrapeResult($date, $stadiumNumber, $raceNumber),
            'A normal call must still trust the (deliberately wrong) cached value.',
        );

        $this->assertSame(
            $correctResult,
            $scraper->scrapeResult($date, $stadiumNumber, $raceNumber, forceRefresh: true),
            'forceRefresh must bypass the cache and return the live, correct value.',
        );

        $this->assertSame(
            $correctResult,
            $scraper->scrapeResult($date, $stadiumNumber, $raceNumber),
            'The forced call must have overwritten the cache entry for subsequent normal calls.',
        );
    }
}
