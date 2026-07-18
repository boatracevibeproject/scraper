<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Enums;

use BVP\Scraper\Enums\Grade;
use BVP\Scraper\Enums\Place;
use BVP\Scraper\Enums\Prefecture;
use BVP\Scraper\Enums\Rank;
use BVP\Scraper\Enums\Stadium;
use BVP\Scraper\Enums\Technique;
use BVP\Scraper\Enums\Weather;
use BVP\Scraper\Enums\WindDirection;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class EnumsTest extends TestCase
{
    public function testGradeFromName(): void
    {
        $this->assertSame(Grade::SG, Grade::fromName('SG'));
        $this->assertNull(Grade::fromName(null));
    }

    public function testGradeFromNameThrowsOnUnknownName(): void
    {
        $this->expectException(ValueError::class);

        Grade::fromName('unknown');
    }

    public function testWeatherShortNameRoundTrip(): void
    {
        $this->assertSame('晴', Weather::晴->shortName());
        $this->assertSame(Weather::晴, Weather::fromShortName('晴'));
    }

    public function testWeatherToArrayShape(): void
    {
        $array = Weather::toArray();

        $this->assertSame(
            ['number' => 1, 'name' => '晴', 'short_name' => '晴'],
            $array[0]
        );
    }

    public function testTechniqueFromName(): void
    {
        $this->assertSame(Technique::逃げ, Technique::fromName('逃げ'));
    }

    public function testWindDirectionFromValue(): void
    {
        $this->assertSame(WindDirection::無風, WindDirection::fromValue(17));
        $this->assertNull(WindDirection::fromValue(null));
    }

    public function testWindDirectionFromValueThrowsOnUnknownValue(): void
    {
        $this->expectException(ValueError::class);

        WindDirection::fromValue(99);
    }

    public function testPlaceNameAndShortNameAreDisplayLabelsNotCaseNames(): void
    {
        $this->assertSame('1着', Place::一着->name());
        $this->assertSame('1', Place::一着->shortName());
        $this->assertSame(Place::一着, Place::fromShortName('1'));
    }

    public function testRankShortNameRoundTrip(): void
    {
        $this->assertSame('A1', Rank::A1級->shortName());
        $this->assertSame(Rank::A1級, Rank::fromShortName('A1'));
    }

    public function testPrefectureShortNameDropsSuffix(): void
    {
        $this->assertSame('東京', Prefecture::東京都->shortName());
    }

    public function testStadiumFromName(): void
    {
        $this->assertSame(Stadium::大村, Stadium::fromName('大村'));
    }
}
