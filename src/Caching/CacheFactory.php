<?php

declare(strict_types=1);

namespace BVP\Scraper\Caching;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

/**
 * @author shimomo
 */
final class CacheFactory
{
    /**
     * @var non-empty-string
     */
    private const string NAMESPACE = 'bvp-scraper';

    /**
     * Builds the default cache backend: a filesystem-backed PSR-16 cache
     * that survives across process runs (unlike an in-memory cache), which
     * matters for backfill workloads that re-run the same date range over
     * multiple invocations.
     *
     * @param ?non-empty-string $directory
     * @return \Psr\SimpleCache\CacheInterface
     */
    public static function createDefault(?string $directory = null): CacheInterface
    {
        return new Psr16Cache(
            new FilesystemAdapter(self::NAMESPACE, 0, $directory)
        );
    }
}
