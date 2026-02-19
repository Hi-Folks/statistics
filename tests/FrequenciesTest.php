<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Statistics;
use PHPUnit\Framework\TestCase;

class FrequenciesTest extends TestCase
{
    public function test_can_calculate_frequencies(): void
    {
        $s = Statistics::make(
            [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76],
        );
        $a = $s->frequencies();
        $this->assertEquals(2, $a[92]);
        $this->assertCount(11, $a);
    }

    public function test_can_calculate_relative_frequencies(): void
    {
        $s = Statistics::make(
            [3, 4, 3, 1],
        );
        $a = $s->relativeFrequencies();
        $this->assertEquals(50, $a[3]);
        $this->assertCount(3, $a);
        $this->assertCount(4, $s->originalArray());
    }

    public function test_can_calculate_cumulative_frequencies(): void
    {
        $s = Statistics::make(
            [3, 4, 3, 1],
        );
        $a = $s->cumulativeFrequencies();
        $this->assertEquals(3, $a[3]);
        $this->assertCount(3, $a);
        $this->assertCount(4, $s->originalArray());
    }

    public function test_can_calculate_cumulative_relative_frequencies(): void
    {
        $s = Statistics::make(
            [3, 4, 3, 1],
        );
        $a = $s->cumulativeRelativeFrequencies();
        $this->assertEquals(75, $a[3]);
        $this->assertCount(3, $a);
        $this->assertCount(4, $s->originalArray());
    }

    public function test_can_calculate_first_quartile(): void
    {
        $s = Statistics::make([3, 4, 3, 1]);
        $this->assertEquals(1.5, $s->firstQuartile());

        $s = Statistics::make([3, 4, 3]);
        $this->assertEquals(3, $s->firstQuartile());
    }

    public function test_can_calculate_first_quartile_with_empty_array(): void
    {
        $s = Statistics::make([]);
        $this->expectException(InvalidDataInputException::class);
        $s->firstQuartile();
    }

    public function test_can_calculate_third_quartile(): void
    {
        $s = Statistics::make([3, 4, 3, 1]);
        $this->assertEquals(3.75, $s->thirdQuartile());

        $s = Statistics::make([3, 4, 3]);
        $this->assertEquals(4, $s->thirdQuartile());
    }

    public function test_can_calculate_third_quartile_with_empty_array(): void
    {
        $s = Statistics::make([]);
        $this->expectException(InvalidDataInputException::class);
        $s->thirdQuartile();
    }
}
