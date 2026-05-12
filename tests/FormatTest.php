<?php

declare(strict_types=1);

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Utils\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test_seconds_to_hms(): void
    {
        $result = Format::secondsToHms(4845);
        $this->assertSame(1, $result['hours']);
        $this->assertSame(20, $result['minutes']);
        $this->assertSame(45, $result['seconds']);
    }

    public function test_seconds_to_hms_zero(): void
    {
        $result = Format::secondsToHms(0);
        $this->assertSame(0, $result['hours']);
        $this->assertSame(0, $result['minutes']);
        $this->assertSame(0, $result['seconds']);
    }

    public function test_seconds_to_hms_with_float(): void
    {
        $result = Format::secondsToHms(3661.7);
        $this->assertSame(1, $result['hours']);
        $this->assertSame(1, $result['minutes']);
        $this->assertSame(1, $result['seconds']);
    }

    public function test_hms_to_seconds(): void
    {
        $this->assertSame(4845, Format::hmsToSeconds(1, 20, 45));
    }

    public function test_hms_to_seconds_zero(): void
    {
        $this->assertSame(0, Format::hmsToSeconds(0, 0, 0));
    }

    public function test_seconds_to_time(): void
    {
        $this->assertSame('1:20:45', Format::secondsToTime(4845));
    }

    public function test_seconds_to_time_with_padding(): void
    {
        $this->assertSame('0:05:03', Format::secondsToTime(303));
    }

    public function test_time_to_seconds(): void
    {
        $this->assertSame(4845, Format::timeToSeconds('1:20:45'));
    }

    public function test_time_to_seconds_with_leading_zeros(): void
    {
        $this->assertSame(4845, Format::timeToSeconds('01:20:45'));
    }

    public function test_round_trip_seconds(): void
    {
        $original = 9280;
        $time = Format::secondsToTime($original);
        $back = Format::timeToSeconds($time);
        $this->assertSame($original, $back);
    }

    public function test_round_trip_hms(): void
    {
        $seconds = Format::hmsToSeconds(2, 35, 10);
        $hms = Format::secondsToHms($seconds);
        $this->assertSame(2, $hms['hours']);
        $this->assertSame(35, $hms['minutes']);
        $this->assertSame(10, $hms['seconds']);
    }

    public function test_time_to_seconds_invalid_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::timeToSeconds('20:45');
    }

    public function test_time_to_seconds_invalid_format_too_many_parts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Format::timeToSeconds('1:20:45:00');
    }
}
