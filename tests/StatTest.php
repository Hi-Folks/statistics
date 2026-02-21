<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Stat;
use PHPUnit\Framework\TestCase;

class StatTest extends TestCase
{
    public function test_calculates_mean(): void
    {
        $this->assertEquals(2.8, Stat::mean([1, 2, 3, 4, 4]));
        $this->assertEquals(2.625, Stat::mean([-1.0, 2.5, 3.25, 5.75]));
        $this->expectException(InvalidDataInputException::class);
        Stat::mean([]);
    }

    public function test_calculates_fmean(): void
    {
        $this->assertEquals(2.8, Stat::mean([1, 2, 3, 4, 4]));
        $this->assertEquals(2.625, Stat::mean([-1.0, 2.5, 3.25, 5.75]));

        $result = Stat::fmean([3.5, 4.0, 5.25]);
        $this->assertIsFloat($result);
        $this->assertEquals(4.25, $result);

        $result = Stat::fmean([85, 92, 83, 91], [0.20, 0.20, 0.30, 0.30], 2);
        $this->assertIsFloat($result);
        $this->assertEquals(87.6, $result);

        $result = Stat::fmean([3.5, 4.0, 5.25], [1, 2, 1]);
        $this->assertIsFloat($result);
        $this->assertEquals(4.1875, $result);

        $result = Stat::fmean([3.5, 4.0, 5.25], precision: 2);
        $this->assertIsFloat($result);
        $this->assertEquals(4.25, $result);

        $result = Stat::fmean([3.5, 4.0, 5.25], [1, 2, 1], precision: 3);
        $this->assertIsFloat($result);
        $this->assertEquals(4.188, $result);
    }

    public function test_calculates_fmean_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::mean([]);
    }

    public function test_calculates_median(): void
    {
        $this->assertEquals(3, Stat::median([1, 3, 5]));
        $this->assertEquals(4, Stat::median([1, 3, 5, 7]));
        $this->assertEquals(1001, Stat::median([1001, 999, 998, 1001, 1002]));
        $this->assertEquals(1001.5, Stat::median([1001, 999, 998, 1003, 1002, 1003]));
        $this->assertEquals(7, Stat::median([1, 3, 5, 7, 9, 11, 13]));
        $this->assertEquals(6, Stat::median([1, 3, 5, 7, 9, 11]));
        $this->assertEquals(1.05, Stat::median([-11, 5.5, -3.4, 7.1, -9, 22]));
        $this->assertEquals(0, Stat::median([-1, -2, -3, -4, 4, 3, 2, 1]));
    }

    public function test_calculates_median_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::median([]);
    }

    public function test_calculates_median_low(): void
    {
        $this->assertEquals(3, Stat::medianLow([1, 3, 5]));
        $this->assertEquals(3, Stat::medianLow([1, 3, 5, 7]));
        $this->assertEquals(1001, Stat::medianLow([1001, 999, 998, 1003, 1002, 1003]));
    }

    public function test_calculates_median_low_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::medianLow([]);
    }

    public function test_calculates_median_high(): void
    {
        $this->assertEquals(3, Stat::medianHigh([1, 3, 5]));
        $this->assertEquals(5, Stat::medianHigh([1, 3, 5, 7]));
        $this->assertEquals(1002, Stat::medianHigh([1001, 999, 998, 1003, 1002, 1003]));
    }

    public function test_calculates_median_high_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::medianHigh([]);
    }

    public function test_calculates_median_grouped(): void
    {
        // Python: median_grouped([1, 2, 2, 3, 4, 4, 4, 4, 4, 5]) == 3.7
        $this->assertEquals(3.7, Stat::medianGrouped([1, 2, 2, 3, 4, 4, 4, 4, 4, 5]));

        // Python: median_grouped([52, 52, 53, 54]) == 52.5
        $this->assertEquals(52.5, Stat::medianGrouped([52, 52, 53, 54]));

        // Python: median_grouped([1, 3, 3, 5, 7]) == 3.25
        $this->assertEquals(3.25, Stat::medianGrouped([1, 3, 3, 5, 7]));

        // With interval=2: median_grouped([1, 3, 3, 5, 7], interval=2) == 3.5
        $this->assertEquals(3.5, Stat::medianGrouped([1, 3, 3, 5, 7], 2));

        // Demographics example from Python docs (interval=10)
        $data = array_merge(
            array_fill(0, 172, 25),
            array_fill(0, 484, 35),
            array_fill(0, 387, 45),
            array_fill(0, 22, 55),
            array_fill(0, 6, 65),
        );
        $this->assertEquals(37.5, round(Stat::medianGrouped($data, 10), 1));

        // Single element: L = 1 - 0.5 = 0.5, result = 0.5 + 1*(0.5-0)/1 = 1.0
        $this->assertEquals(1.0, Stat::medianGrouped([1]));
    }

    public function test_calculates_median_grouped_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::medianGrouped([]);
    }

    public function test_calculates_mode(): void
    {
        $this->assertEquals(3, Stat::mode([1, 1, 2, 3, 3, 3, 3, 4]));
        $this->assertNull(Stat::mode([1, 2, 3]));
        $this->assertEquals('red', Stat::mode(['red', 'blue', 'blue', 'red', 'green', 'red', 'red']));
    }

    public function test_calculates_mode_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::mode([]);
    }

    public function test_calculates_multimode(): void
    {
        $this->assertEquals([3], Stat::multimode([1, 1, 2, 3, 3, 3, 3, 4]));
        $this->assertEquals([1, 3], Stat::multimode([1, 1, 2, 3, 3, 3, 3, 1, 1, 4]));
        $result = Stat::multimode(str_split('aabbbbccddddeeffffgg'));
        $this->assertNotNull($result);
        $this->assertEquals(['b', 'd', 'f'], $result);
        $this->assertCount(3, $result);
        $this->assertEquals('b', $result[0]);
        $this->assertEquals('d', $result[1]);
        $this->assertEquals('f', $result[2]);
    }

    public function test_calculates_multimode_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::multimode([]);
    }

    public function test_calculates_population_standard_deviation(): void
    {
        $this->assertEquals(0.986893273527251, Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75]));
        $this->assertEquals(2.4495, Stat::pstdev([1, 2, 4, 5, 8], 4));
        $this->assertEquals(0, Stat::pstdev([1]));
        $this->assertEquals(0.8291562, Stat::pstdev([1, 2, 3, 3], 7));
    }

    public function test_calculates_population_standard_deviation_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::pstdev([]);
    }

    public function test_calculates_sample_standard_deviation(): void
    {
        $this->assertEquals(1.0810874155219827, Stat::stdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75]));
        $this->assertEquals(2, Stat::stdev([1, 2, 2, 4, 6]));
        $this->assertEquals(2.7386, Stat::stdev([1, 2, 4, 5, 8], 4));
    }

    public function test_calculates_sample_standard_deviation_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::stdev([]);
    }

    public function test_calculates_sample_standard_deviation_with_single_element(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::stdev([1]);
    }

    public function test_calculates_variance(): void
    {
        $this->assertEquals(1.3720238095238095, Stat::variance([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5]));
    }

    public function test_calculates_pvariance(): void
    {
        $this->assertEquals(1.25, Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25]));
        $this->assertEquals(0.6875, Stat::pvariance([1, 2, 3, 3]));
    }

    public function test_calculates_geometric_mean(): void
    {
        $this->assertEquals(36, Stat::geometricMean([54, 24, 36], 2));
    }

    public function test_calculates_geometric_mean_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::geometricMean([]);
    }

    public function test_calculates_harmonic_mean(): void
    {
        $this->assertEquals(48, Stat::harmonicMean([40, 60], round: 2));
        $this->assertEquals(0, Stat::harmonicMean([10, 100, 0, 1]));
        $this->assertEquals(56, Stat::harmonicMean([40, 60], [5, 30]));
        $this->assertEquals(52.2, Stat::harmonicMean([60, 40], [7, 3], 1));
    }

    public function test_calculates_harmonic_mean_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::harmonicMean([]);
    }

    public function test_calculates_quantiles(): void
    {
        $q = Stat::quantiles([98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76]);
        $this->assertEquals(58.75, $q[0]);
        $this->assertEquals(85.5, $q[1]);
        $this->assertEquals(92, $q[2]);

        $q = Stat::quantiles([98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88]);
        $this->assertEquals(55, $q[0]);
        $this->assertEquals(88, $q[1]);
        $this->assertEquals(92, $q[2]);

        $q = Stat::quantiles([1, 2]);
        $this->assertEquals(0.75, $q[0]);
        $this->assertEquals(1.5, $q[1]);
        $this->assertEquals(2.25, $q[2]);

        $q = Stat::quantiles([1, 2, 4]);
        $this->assertEquals(1, $q[0]);
        $this->assertEquals(2, $q[1]);
        $this->assertEquals(4, $q[2]);
    }

    public function test_calculates_quantiles_with_too_few_elements(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::quantiles([1]);
    }

    public function test_calculates_quantiles_with_invalid_n(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::quantiles([1, 2, 3], 0);
    }

    public function test_calculates_first_quartile(): void
    {
        $q = Stat::firstQuartile([98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76]);
        $this->assertEquals(58.75, $q);
    }

    public function test_calculates_first_quartile_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::firstQuartile([]);
    }

    public function test_calculates_covariance(): void
    {
        $covariance = Stat::covariance(
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 1, 2, 3, 1, 2, 3],
        );
        $this->assertEquals(0.75, $covariance);

        $covariance = Stat::covariance(
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
        );
        $this->assertEquals(-7.5, $covariance);

        $covariance = Stat::covariance(
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
        );
        $this->assertEquals(-7.5, $covariance);
    }

    public function test_calculates_covariance_wrong_usage(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::covariance(
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
            [1, 2, 3, 4, 5, 6, 7, 8],
        );
    }

    public function test_calculates_covariance_with_empty_arrays(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::covariance([], []);
    }

    public function test_calculates_covariance_with_single_element(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::covariance([3], [3]);
    }

    public function test_calculates_covariance_with_non_numeric_first(): void
    {
        $this->expectException(InvalidDataInputException::class);
        // Intentionally passing non-numeric values to test exception handling
        Stat::covariance(['a', 1], ['b', 2]); // @phpstan-ignore argument.type, argument.type
    }

    public function test_calculates_covariance_with_non_numeric_second(): void
    {
        $this->expectException(InvalidDataInputException::class);
        // Intentionally passing non-numeric values to test exception handling
        Stat::covariance([3, 1], ['b', 2]); // @phpstan-ignore argument.type
    }

    public function test_calculates_correlation(): void
    {
        $correlation = Stat::correlation(
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
        );
        $this->assertIsFloat($correlation);
        $this->assertEquals(1, $correlation);

        $correlation = Stat::correlation(
            [1, 2, 3, 4, 5, 6, 7, 8, 9],
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
        );
        $this->assertIsFloat($correlation);
        $this->assertEquals(-1, $correlation);

        $correlation = Stat::correlation(
            [3, 6, 9],
            [70, 75, 80],
        );
        $this->assertIsFloat($correlation);
        $this->assertEquals(1, $correlation);

        $correlation = Stat::correlation(
            [20, 23, 8, 29, 14, 11, 11, 20, 17, 17],
            [30, 35, 21, 33, 33, 26, 22, 31, 33, 36],
        );
        $this->assertIsFloat($correlation);
        $this->assertEquals(0.71, $correlation);
    }

    public function test_calculates_correlation_wrong_usage_different_lengths(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::correlation(
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
            [1, 2, 3, 4, 5, 6, 7, 8],
        );
    }

    public function test_calculates_correlation_wrong_usage_empty(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::correlation([], []);
    }

    public function test_calculates_correlation_wrong_usage_single(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::correlation([3], [3]);
    }

    public function test_calculates_correlation_wrong_usage_constant(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::correlation([3, 1, 2], [2, 2, 2]);
    }

    public function test_calculates_linear_regression(): void
    {
        [$slope, $intercept] = Stat::linearRegression(
            [1971, 1975, 1979, 1982, 1983],
            [1, 2, 3, 4, 5],
        );
        $this->assertIsFloat($slope);
        $this->assertEquals(0.31, $slope);
        $this->assertIsFloat($intercept);
        $this->assertEquals(-610.18, round($intercept, 2));

        [$slope, $intercept] = Stat::linearRegression(
            [1971, 1975, 1979, 1982, 1983],
            [1, 2, 1, 3, 1],
        );
        $this->assertIsFloat($slope);
        $this->assertEquals(0.05, $slope);
        $this->assertIsFloat($intercept);
        $this->assertEquals(-97.3, $intercept);
        $this->assertEquals(4, round($slope * 2019 + $intercept));
    }

    public function test_calculates_linear_regression_with_single_element(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::linearRegression([3], [2]);
    }

    public function test_calculates_linear_regression_with_different_lengths(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::linearRegression([3, 3, 3, 3], [2, 1, 1, 1, 1]);
    }

    public function test_calculates_linear_regression_with_constant_x(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::linearRegression([3, 3, 3, 3, 3], [1, 1, 1, 1, 1]);
    }
}
