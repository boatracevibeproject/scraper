<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Caching;

use BVP\Scraper\Caching\DateBasedCachePolicy;
use Carbon\CarbonImmutable as Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class DateBasedCachePolicyTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private DateBasedCachePolicy $policy;

    #[\Override]
    protected function setUp(): void
    {
        $this->policy = new DateBasedCachePolicy('Asia/Tokyo');
    }

    public function testPastDateIsCacheable(): void
    {
        $yesterday = Carbon::now('Asia/Tokyo')->subDay();

        $this->assertTrue($this->policy->isCacheable('result', $yesterday));
    }

    public function testTodayIsNotCacheable(): void
    {
        $today = Carbon::now('Asia/Tokyo');

        $this->assertFalse($this->policy->isCacheable('result', $today));
    }

    public function testFutureDateIsNotCacheable(): void
    {
        $tomorrow = Carbon::now('Asia/Tokyo')->addDay();

        $this->assertFalse($this->policy->isCacheable('program', $tomorrow));
    }
}
