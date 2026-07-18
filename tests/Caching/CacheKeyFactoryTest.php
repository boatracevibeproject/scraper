<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Caching;

use BVP\Scraper\Caching\CacheKeyFactory;
use Carbon\CarbonImmutable as Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class CacheKeyFactoryTest extends TestCase
{
    public function testMakeWithoutBetType(): void
    {
        $key = CacheKeyFactory::make('result', Carbon::parse('2024-05-01'), 6, 3);

        $this->assertSame('result.20240501.6.3', $key);
    }

    public function testMakeWithBetType(): void
    {
        $key = CacheKeyFactory::make('odds', Carbon::parse('2024-05-01'), 24, 12, 'trifecta');

        $this->assertSame('odds.20240501.24.12.trifecta', $key);
    }

    public function testMakeForStadium(): void
    {
        $key = CacheKeyFactory::makeForStadium(Carbon::parse('2024-05-01'));

        $this->assertSame('stadium.20240501', $key);
    }

    public function testDifferentInputsProduceDifferentKeys(): void
    {
        $a = CacheKeyFactory::make('result', Carbon::parse('2024-05-01'), 6, 3);
        $b = CacheKeyFactory::make('result', Carbon::parse('2024-05-01'), 6, 4);

        $this->assertNotSame($a, $b);
    }
}
