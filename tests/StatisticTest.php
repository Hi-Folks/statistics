<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Statistics;
use PHPUnit\Framework\TestCase;

class StatisticTest extends TestCase
{
    public function test_can_calculate_statistics(): void
    {
        $s = Statistics::make(
            [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76],
        );
        $this->assertEquals(12, $s->count());
        $this->assertEquals(85.5, $s->median());
        $this->assertEquals(58.75, $s->firstQuartile());
        $this->assertEquals(92, $s->thirdQuartile());
        $this->assertEquals(33.25, $s->interquartileRange());
        $this->assertCount(12, $s->originalArray());

        $s = Statistics::make(
            [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88],
        );
        $this->assertEquals(11, $s->count());
        $this->assertEquals(88, $s->median());
        $this->assertEquals(55, $s->firstQuartile());
        $this->assertEquals(92, $s->thirdQuartile());
        $this->assertEquals(37, $s->interquartileRange());
        $this->assertCount(11, $s->originalArray());
    }

    public function test_can_calculate_statistics_again(): void
    {
        $s = Statistics::make(
            [3, 5, 4, 7, 5, 2],
        );
        $this->assertEquals(6, $s->count());
        $this->assertEquals(13 / 3, $s->mean());
        $this->assertEquals(4.5, $s->median());
        $this->assertEquals(5, $s->mode());
        $this->assertEquals(2, $s->min());
        $this->assertEquals(7, $s->max());
        $this->assertEquals(5, $s->range());
        $this->assertEquals(2.75, $s->firstQuartile());
        $this->assertEquals(5.5, $s->thirdQuartile());
    }

    public function test_can_calculate_statistics_again_and_again(): void
    {
        $s = Statistics::make(
            [13, 18, 13, 14, 13, 16, 14, 21, 13],
        );
        $this->assertEquals(9, $s->count());
        $this->assertEquals(15, $s->mean());
        $this->assertEquals(14, $s->median());
        $this->assertEquals(13, $s->mode());
        $this->assertEquals(13, $s->min());
        $this->assertEquals(21, $s->max());
        $this->assertEquals(8, $s->range());
        $this->assertEquals(13, $s->firstQuartile());
        $this->assertEquals(17, $s->thirdQuartile());

        $s = Statistics::make(
            [1, 2, 4, 7],
        );
        $this->assertEquals(4, $s->count());
        $this->assertEquals(3.5, $s->mean());
        $this->assertEquals(3, $s->median());
        $this->assertNull($s->mode());
        $this->assertEquals(1, $s->min());
        $this->assertEquals(7, $s->max());
        $this->assertEquals(6, $s->range());
    }

    public function test_can_strip_zeros(): void
    {
        $s = Statistics::make(
            [3, 5, 0, 0.1, 4, 7, 5, 2],
        )->stripZeroes();
        $this->assertEquals(7, $s->count());
    }

    public function test_can_calculate_mean(): void
    {
        $s = Statistics::make(
            [3, 5, 4, 7, 5, 2],
        );
        $this->assertEquals(6, $s->count());
        $this->assertEquals(13 / 3, $s->mean());

        $s = Statistics::make([]);
        $this->assertEquals(0, $s->count());
        $this->expectException(InvalidDataInputException::class);
        $s->mean();
    }

    public function test_can_calculate_mean_again(): void
    {
        $s = Statistics::make([1, 2, 3, 4, 4]);
        $this->assertEquals(2.8, $s->mean());

        $s = Statistics::make([-1.0, 2.5, 3.25, 5.75]);
        $this->assertEquals(2.625, $s->mean());

        $s = Statistics::make([0.5, 0.75, 0.625, 0.375]);
        $this->assertEquals(0.5625, $s->mean());

        $s = Statistics::make([3.5, 4.0, 5.25]);
        $this->assertEquals(4.25, $s->mean());
    }

    public function test_can_values_to_string(): void
    {
        $s = Statistics::make([1, 2, 3, 4, 4]);
        $this->assertEquals('1,2,3,4,4', $s->valuesToString(false));
        $this->assertEquals('1,2,3', $s->valuesToString(3));
    }

    public function test_calculates_population_standard_deviation(): void
    {
        $this->assertEquals(
            0.986893273527251,
            Statistics::make([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])->pstdev(),
        );
        $this->assertEquals(
            2.4495,
            Statistics::make([1, 2, 4, 5, 8])->pstdev(4),
        );
        $this->assertEquals(0, Statistics::make([1])->pstdev());
        $this->assertEquals(0.8291562, Statistics::make([1, 2, 3, 3])->pstdev(7));
    }

    public function test_calculates_population_standard_deviation_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([])->pstdev();
    }

    public function test_calculates_sample_standard_deviation(): void
    {
        $this->assertEquals(
            1.0810874155219827,
            Statistics::make([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])->stdev(),
        );
        $this->assertEquals(2, Statistics::make([1, 2, 2, 4, 6])->stdev());
        $this->assertEquals(2.7386, Statistics::make([1, 2, 4, 5, 8])->stdev(4));
    }

    public function test_calculates_sample_standard_deviation_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([])->stdev();
    }

    public function test_calculates_sample_standard_deviation_with_single_element(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([1])->stdev();
    }

    public function test_calculates_variance(): void
    {
        $this->assertEquals(
            1.3720238095238095,
            Statistics::make([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5])->variance(),
        );
    }

    public function test_calculates_pvariance(): void
    {
        $this->assertEquals(
            1.25,
            Statistics::make([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25])->pvariance(),
        );
        $this->assertEquals(0.6875, Statistics::make([1, 2, 3, 3])->pvariance());
    }

    public function test_calculates_skewness(): void
    {
        $this->assertEqualsWithDelta(0.0, Statistics::make([1, 2, 3, 4, 5])->skewness(), 1e-10);
    }

    public function test_calculates_pskewness(): void
    {
        $this->assertEqualsWithDelta(0.0, Statistics::make([1, 2, 3, 4, 5])->pskewness(), 1e-10);
    }

    public function test_calculates_kurtosis(): void
    {
        // Uniform-like data: negative excess kurtosis
        $this->assertLessThan(0, Statistics::make([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->kurtosis());
    }

    public function test_calculates_geometric_mean(): void
    {
        $this->assertEquals(36, Statistics::make([54, 24, 36])->geometricMean(2));
        $this->assertEquals(6.81, Statistics::make([4, 8, 3, 9, 17])->geometricMean(2));
    }

    public function test_calculates_geometric_mean_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([])->geometricMean();
    }

    public function test_calculates_harmonic_mean(): void
    {
        $this->assertEquals(48.0, Statistics::make([40, 60])->harmonicMean(1));
    }

    public function test_calculates_harmonic_mean_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([])->harmonicMean();
    }

    public function test_can_distinct_numeric_array(): void
    {
        $this->assertEquals([1, 2, 3], Statistics::make([1, 2, 3])->numericalArray());
        $this->assertEquals([1, '2', 3], Statistics::make([1, '2', 3])->numericalArray());
        $this->assertEquals([], Statistics::make([])->numericalArray());
        $this->expectException(InvalidDataInputException::class);
        Statistics::make([1, 'some string', 3])->numericalArray();
    }

    public function test_median_grouped(): void
    {
        $result = Statistics::make([1, 2, 2, 3, 4, 4, 4, 5])->medianGrouped();
        $this->assertIsFloat($result);
        $this->assertEquals(3.5, $result);
    }

    public function test_max_with_empty_array(): void
    {
        $this->assertEquals(0, Statistics::make([])->max());
    }

    public function test_min_with_empty_array(): void
    {
        $this->assertEquals(0, Statistics::make([])->min());
    }
}
