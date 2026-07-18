<?php

declare(strict_types=1);

namespace BVP\Scraper\Tests\Converters;

use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Enums\Weather;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ConverterTest extends TestCase
{
    public function testToInt(): void
    {
        $this->assertSame(3, Converter::toInt('3'));
        $this->assertNull(Converter::toInt(null));
    }

    public function testToFloat(): void
    {
        $this->assertSame(1.5, Converter::toFloat('1.5'));
        $this->assertNull(Converter::toFloat(null));
    }

    public function testToString(): void
    {
        $this->assertSame('3', Converter::toString(3));
        $this->assertNull(Converter::toString(null));
    }

    public function testToNull(): void
    {
        $this->assertNull(Converter::toNull('anything'));
    }

    public function testTrimHandlesAsciiAndFullWidthSpace(): void
    {
        $this->assertSame('foo', Converter::trim("  foo  \n"));
        $this->assertSame('bar', Converter::trim("\u{3000}bar\u{3000}"));
        $this->assertNull(Converter::trim(null));
    }

    public function testToDayNumber(): void
    {
        $this->assertSame(3, Converter::toDayNumber(' 3 '));
        $this->assertNull(Converter::toDayNumber(null));
    }

    public function testToCamelCase(): void
    {
        $this->assertSame('windSpeed', Converter::toCamelCase('wind_speed'));
    }

    public function testToCamelCaseKeys(): void
    {
        $this->assertSame(
            ['windSpeed' => 3, 'waveHeight' => 1],
            Converter::toCamelCaseKeys(['wind_speed' => 3, 'wave_height' => 1])
        );
    }

    public function testToEnumOrNullReturnsCaseOnSuccess(): void
    {
        $result = Converter::toEnumOrNull(fn() => Weather::fromName('晴'));

        $this->assertSame(Weather::晴, $result);
    }

    public function testToEnumOrNullReturnsNullOnValueError(): void
    {
        $result = Converter::toEnumOrNull(fn() => Weather::fromName('不明'));

        $this->assertNull($result);
    }
}
