<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Enums\Alternative;
use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Enums\KdeKernel;
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

    public function test_fmean_empty_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::fmean([]);
    }

    public function test_fmean_mismatched_weights_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::fmean([1, 2, 3], [1, 2]);
    }

    public function test_fmean_zero_weight_sum_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::fmean([1, 2, 3], [0, 0, 0]);
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

    public function test_calculates_variance_with_precomputed_mean(): void
    {
        $data = [2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5];
        $mean = Stat::mean($data);
        $this->assertEquals(
            Stat::variance($data),
            Stat::variance($data, xbar: $mean),
        );
    }

    public function test_calculates_pvariance(): void
    {
        $this->assertEquals(1.25, Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25]));
        $this->assertEquals(0.6875, Stat::pvariance([1, 2, 3, 3]));
    }

    public function test_calculates_pvariance_with_precomputed_mean(): void
    {
        $data = [0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25];
        $mean = Stat::mean($data);
        $this->assertEquals(
            Stat::pvariance($data),
            Stat::pvariance($data, mu: $mean),
        );
    }

    public function test_calculates_skewness_symmetric(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::skewness([1, 2, 3, 4, 5]), 1e-10);
    }

    public function test_calculates_skewness_right_skewed(): void
    {
        $skewness = Stat::skewness([1, 1, 1, 1, 1, 10]);
        $this->assertGreaterThan(0, $skewness);
    }

    public function test_calculates_skewness_left_skewed(): void
    {
        $skewness = Stat::skewness([1, 10, 10, 10, 10, 10]);
        $this->assertLessThan(0, $skewness);
    }

    public function test_calculates_skewness_with_rounding(): void
    {
        $skewness = Stat::skewness([1, 1, 1, 1, 1, 10], 4);
        $this->assertGreaterThan(0, $skewness);
        $this->assertEquals(round($skewness, 4), $skewness);
    }

    public function test_skewness_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::skewness([]);
    }

    public function test_skewness_with_two_elements(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::skewness([1, 2]);
    }

    public function test_skewness_with_identical_values(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::skewness([5, 5, 5, 5]);
    }

    public function test_calculates_pskewness_symmetric(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::pskewness([1, 2, 3, 4, 5]), 1e-10);
    }

    public function test_calculates_pskewness_right_skewed(): void
    {
        $pskewness = Stat::pskewness([1, 1, 1, 1, 1, 10]);
        $this->assertGreaterThan(0, $pskewness);
    }

    public function test_calculates_pskewness_left_skewed(): void
    {
        $pskewness = Stat::pskewness([1, 10, 10, 10, 10, 10]);
        $this->assertLessThan(0, $pskewness);
    }

    public function test_calculates_pskewness_with_rounding(): void
    {
        $pskewness = Stat::pskewness([1, 1, 1, 1, 1, 10], 4);
        $this->assertGreaterThan(0, $pskewness);
        $this->assertEquals(round($pskewness, 4), $pskewness);
    }

    public function test_pskewness_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::pskewness([]);
    }

    public function test_pskewness_with_two_elements(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::pskewness([1, 2]);
    }

    public function test_pskewness_with_identical_values(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::pskewness([5, 5, 5, 5]);
    }

    public function test_pskewness_less_than_skewness_for_small_samples(): void
    {
        $data = [1, 1, 1, 1, 1, 10];
        $skewness = Stat::skewness($data);
        $pskewness = Stat::pskewness($data);
        // Population skewness magnitude is smaller than sample skewness for small n
        $this->assertLessThan(abs($skewness), abs($pskewness));
    }

    public function test_calculates_kurtosis_normal_like(): void
    {
        // A uniform-ish symmetric dataset: excess kurtosis near 0 or negative
        $this->assertEqualsWithDelta(-1.2, Stat::kurtosis([1, 2, 3, 4, 5]), 0.1);
    }

    public function test_calculates_kurtosis_heavy_tails(): void
    {
        // Data with outliers should have positive excess kurtosis (leptokurtic)
        $kurtosis = Stat::kurtosis([1, 2, 2, 2, 2, 2, 2, 2, 2, 50]);
        $this->assertGreaterThan(0, $kurtosis);
    }

    public function test_calculates_kurtosis_light_tails(): void
    {
        // Uniform-like data should have negative excess kurtosis (platykurtic)
        $kurtosis = Stat::kurtosis([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $this->assertLessThan(0, $kurtosis);
    }

    public function test_calculates_kurtosis_with_rounding(): void
    {
        $kurtosis = Stat::kurtosis([1, 2, 2, 2, 2, 2, 2, 2, 2, 50], 4);
        $this->assertEquals(round($kurtosis, 4), $kurtosis);
    }

    public function test_kurtosis_with_empty_array(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kurtosis([]);
    }

    public function test_kurtosis_with_three_elements(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kurtosis([1, 2, 3]);
    }

    public function test_kurtosis_with_identical_values(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kurtosis([5, 5, 5, 5]);
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

    public function test_calculates_quantiles_inclusive(): void
    {
        $q = Stat::quantiles([1, 2, 3, 4, 5], method: 'inclusive');
        $this->assertEquals(2.0, $q[0]);
        $this->assertEquals(3.0, $q[1]);
        $this->assertEquals(4.0, $q[2]);

        $q = Stat::quantiles([1, 2, 3, 4, 5], 10, method: 'inclusive');
        $this->assertEquals(1.4, $q[0]);
        $this->assertEquals(1.8, $q[1]);
        $this->assertEquals(2.2, $q[2]);
        $this->assertEquals(2.6, $q[3]);
        $this->assertEquals(3.0, $q[4]);
        $this->assertEquals(3.4, $q[5]);
        $this->assertEquals(3.8, $q[6]);
        $this->assertEquals(4.2, $q[7]);
        $this->assertEquals(4.6, $q[8]);
    }

    public function test_calculates_quantiles_with_invalid_method(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::quantiles([1, 2, 3], method: 'invalid');
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

    public function test_calculates_spearman_correlation(): void
    {
        // Monotonic relationship: ranks are perfectly correlated
        $correlation = Stat::correlation(
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            'ranked',
        );
        $this->assertIsFloat($correlation);
        $this->assertEqualsWithDelta(1.0, $correlation, 1e-9);

        // Inverse monotonic relationship
        $correlation = Stat::correlation(
            [1, 2, 3, 4, 5],
            [10, 8, 6, 4, 2],
            'ranked',
        );
        $this->assertIsFloat($correlation);
        $this->assertEqualsWithDelta(-1.0, $correlation, 1e-9);

        // Non-linear but monotonic: Spearman = 1, Pearson < 1
        $correlation = Stat::correlation(
            [1, 2, 3, 4, 5],
            [1, 4, 9, 16, 25],
            'ranked',
        );
        $this->assertIsFloat($correlation);
        $this->assertEqualsWithDelta(1.0, $correlation, 1e-9);
    }

    public function test_calculates_spearman_correlation_planets(): void
    {
        // Python docs example: planetary orbital periods and distances from the sun
        $orbitalPeriod = [88, 225, 365, 687, 4331, 10_756, 30_687, 60_190];
        $distFromSun = [58, 108, 150, 228, 778, 1_400, 2_900, 4_500];

        // Perfect monotonic relationship → Spearman = 1.0
        $correlation = Stat::correlation($orbitalPeriod, $distFromSun, 'ranked');
        $this->assertEqualsWithDelta(1.0, $correlation, 1e-9);

        // Linear (Pearson) correlation is imperfect
        $correlation = Stat::correlation($orbitalPeriod, $distFromSun);
        $this->assertIsFloat($correlation);
        $this->assertEquals(0.9882, round($correlation, 4));

        // Kepler's third law: linear correlation between
        // the square of the period and the cube of the distance
        $periodSquared = array_map(fn(int $p): int => $p * $p, $orbitalPeriod);
        $distCubed = array_map(fn(int $d): int => $d * $d * $d, $distFromSun);
        $correlation = Stat::correlation($periodSquared, $distCubed);
        $this->assertIsFloat($correlation);
        $this->assertEquals(1.0, round($correlation, 4));
    }

    public function test_calculates_spearman_correlation_with_ties(): void
    {
        // Ties should receive average ranks
        $correlation = Stat::correlation(
            [1, 2, 2, 3],
            [10, 20, 20, 30],
            'ranked',
        );
        $this->assertIsFloat($correlation);
        $this->assertEqualsWithDelta(1.0, $correlation, 1e-9);
    }

    public function test_calculates_correlation_invalid_method(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::correlation(
            [1, 2, 3],
            [4, 5, 6],
            'invalid',
        );
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

    public function test_calculates_proportional_linear_regression(): void
    {
        [$slope, $intercept] = Stat::linearRegression(
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            proportional: true,
        );
        $this->assertIsFloat($slope);
        $this->assertEquals(2.0, $slope);
        $this->assertSame(0.0, $intercept);

        [$slope, $intercept] = Stat::linearRegression(
            [1, 2, 3, 4, 5],
            [3, 5, 7, 9, 11],
            proportional: true,
        );
        $this->assertIsFloat($slope);
        $this->assertEquals(2.27, round($slope, 2));
        $this->assertSame(0.0, $intercept);
    }

    public function test_proportional_linear_regression_with_all_zeros_x(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::linearRegression([0, 0, 0, 0, 0], [1, 2, 3, 4, 5], proportional: true);
    }

    public function test_r_squared_perfect_fit(): void
    {
        $r2 = Stat::rSquared([1, 2, 3, 4, 5], [2, 4, 6, 8, 10]);
        $this->assertEqualsWithDelta(1.0, $r2, 1e-10);
    }

    public function test_r_squared_real_data(): void
    {
        $r2 = Stat::rSquared(
            [1971, 1975, 1979, 1982, 1983],
            [1, 2, 3, 4, 5],
        );
        $this->assertEqualsWithDelta(0.961, round($r2, 4), 1e-4);
    }

    public function test_r_squared_with_rounding(): void
    {
        $r2 = Stat::rSquared(
            [1971, 1975, 1979, 1982, 1983],
            [1, 2, 3, 4, 5],
            round: 2,
        );
        $this->assertSame(0.96, $r2);
    }

    public function test_r_squared_proportional(): void
    {
        $r2 = Stat::rSquared(
            [1, 2, 3, 4, 5],
            [2, 4, 6, 8, 10],
            proportional: true,
        );
        $this->assertEqualsWithDelta(1.0, $r2, 1e-10);
    }

    public function test_r_squared_with_different_lengths(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::rSquared([1, 2, 3], [1, 2]);
    }

    public function test_r_squared_with_single_element(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::rSquared([1], [2]);
    }

    public function test_r_squared_with_constant_y(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::rSquared([1, 2, 3, 4, 5], [3, 3, 3, 3, 3]);
    }

    public function test_confidence_interval_95(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        [$lower, $upper] = Stat::confidenceInterval($data);
        // mean = 5.0, stdev ≈ 2.1381, sem ≈ 0.7559, z = 1.96
        // margin ≈ 1.4815
        $this->assertEqualsWithDelta(3.5185, $lower, 0.01);
        $this->assertEqualsWithDelta(6.4815, $upper, 0.01);
    }

    public function test_confidence_interval_99(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        [$lower, $upper] = Stat::confidenceInterval($data, confidenceLevel: 0.99);
        // 99% CI is wider than 95% CI
        $this->assertLessThan(3.5, $lower);
        $this->assertGreaterThan(6.5, $upper);
    }

    public function test_confidence_interval_with_rounding(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        [$lower, $upper] = Stat::confidenceInterval($data, round: 2);
        $this->assertSame(3.52, $lower);
        $this->assertSame(6.48, $upper);
    }

    public function test_confidence_interval_narrows_with_more_data(): void
    {
        $small = [2, 4, 4, 4, 5, 5, 7, 9];
        $large = [2, 4, 4, 4, 5, 5, 7, 9, 3, 4, 5, 6, 4, 5, 6, 5];
        [$sLower, $sUpper] = Stat::confidenceInterval($small);
        [$lLower, $lUpper] = Stat::confidenceInterval($large);
        $this->assertLessThan($sUpper - $sLower, $lUpper - $lLower);
    }

    public function test_confidence_interval_single_element_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval([42]);
    }

    public function test_confidence_interval_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval([]);
    }

    public function test_confidence_interval_invalid_confidence_level_throws(): void
    {
        $data = [1, 2, 3, 4, 5];
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval($data, confidenceLevel: 0.0);
    }

    public function test_confidence_interval_confidence_level_one_throws(): void
    {
        $data = [1, 2, 3, 4, 5];
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval($data, confidenceLevel: 1.0);
    }

    public function test_confidence_interval_confidence_level_above_one_throws(): void
    {
        $data = [1, 2, 3, 4, 5];
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval($data, confidenceLevel: 1.5);
    }

    public function test_confidence_interval_negative_confidence_level_throws(): void
    {
        $data = [1, 2, 3, 4, 5];
        $this->expectException(InvalidDataInputException::class);
        Stat::confidenceInterval($data, confidenceLevel: -0.1);
    }

    // --- zTest ---

    public function test_z_test_two_sided(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::zTest($data, 3.0);
        // mean = 5.0, sem = stdev/sqrt(8) ≈ 0.7559
        // zScore = (5.0 - 3.0) / 0.7559 ≈ 2.6458
        $this->assertArrayHasKey('zScore', $result);
        $this->assertArrayHasKey('pValue', $result);
        $this->assertEqualsWithDelta(2.6458, $result['zScore'], 0.001);
        $this->assertLessThan(0.05, $result['pValue']);
    }

    public function test_z_test_greater(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::zTest($data, 3.0, Alternative::Greater);
        // One-tailed p-value should be roughly half the two-sided
        $twoSided = Stat::zTest($data, 3.0);
        $this->assertEqualsWithDelta($twoSided['pValue'] / 2, $result['pValue'], 0.001);
    }

    public function test_z_test_less(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        // mean=5 > populationMean=3, so P(Z < zScore) should be large
        $result = Stat::zTest($data, 3.0, Alternative::Less);
        $this->assertGreaterThan(0.95, $result['pValue']);
    }

    public function test_z_test_non_significant(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        // populationMean close to sample mean (5.0)
        $result = Stat::zTest($data, 5.0);
        $this->assertEqualsWithDelta(0.0, $result['zScore'], 1e-10);
        $this->assertEqualsWithDelta(1.0, $result['pValue'], 0.01);
    }

    public function test_z_test_with_rounding(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::zTest($data, 3.0, round: 2);
        $this->assertSame(round($result['zScore'], 2), $result['zScore']);
        $this->assertSame(round($result['pValue'], 2), $result['pValue']);
    }

    public function test_z_test_single_element_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::zTest([42], 40.0);
    }

    public function test_z_test_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::zTest([], 0.0);
    }

    // --- tTest ---

    public function test_t_test_two_sided(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::tTest($data, 3.0);
        // mean = 5.0, sem = stdev/sqrt(8) ≈ 0.7559
        // tStatistic = (5.0 - 3.0) / 0.7559 ≈ 2.6458
        $this->assertArrayHasKey('tStatistic', $result);
        $this->assertArrayHasKey('pValue', $result);
        $this->assertArrayHasKey('degreesOfFreedom', $result);
        $this->assertEqualsWithDelta(2.6458, $result['tStatistic'], 0.001);
        $this->assertLessThan(0.05, $result['pValue']);
        $this->assertEquals(7, $result['degreesOfFreedom']);
    }

    public function test_t_test_greater(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::tTest($data, 3.0, Alternative::Greater);
        // One-tailed p-value should be roughly half the two-sided
        $twoSided = Stat::tTest($data, 3.0);
        $this->assertEqualsWithDelta($twoSided['pValue'] / 2, $result['pValue'], 0.001);
    }

    public function test_t_test_less(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        // mean=5 > populationMean=3, so P(T < tStatistic) should be large
        $result = Stat::tTest($data, 3.0, Alternative::Less);
        $this->assertGreaterThan(0.95, $result['pValue']);
    }

    public function test_t_test_non_significant(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        // populationMean close to sample mean (5.0)
        $result = Stat::tTest($data, 5.0);
        $this->assertEqualsWithDelta(0.0, $result['tStatistic'], 1e-10);
        $this->assertEqualsWithDelta(1.0, $result['pValue'], 0.01);
    }

    public function test_t_test_degrees_of_freedom(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $result = Stat::tTest($data, 5.0);
        $this->assertEquals(9, $result['degreesOfFreedom']);
    }

    public function test_t_test_large_sample_converges_to_z_test(): void
    {
        // With a large sample, t-test p-value should approximate z-test p-value
        $data = range(1, 100);
        $tResult = Stat::tTest($data, 45.0);
        $zResult = Stat::zTest($data, 45.0);
        $this->assertEqualsWithDelta($zResult['pValue'], $tResult['pValue'], 0.01);
    }

    public function test_t_test_with_rounding(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::tTest($data, 3.0, round: 2);
        $this->assertSame(round($result['tStatistic'], 2), $result['tStatistic']);
        $this->assertSame(round($result['pValue'], 2), $result['pValue']);
    }

    public function test_t_test_single_element_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::tTest([42], 40.0);
    }

    public function test_t_test_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::tTest([], 0.0);
    }

    public function test_kde_normal(): void
    {
        $data = [-2.1, -1.3, -0.4, 1.9, 5.1, 6.2];
        $f = Stat::kde($data, 1.5);
        $this->assertIsCallable($f);

        $density = $f(2.5);
        $this->assertIsFloat($density);
        $this->assertGreaterThan(0, $density);

        // Verify against manually computed value
        // f(2.5) = (1/(6*1.5)) * sum of K((2.5 - xi)/1.5)
        $n = count($data);
        $h = 1.5;
        $sum = 0.0;
        $sqrt2pi = sqrt(2.0 * M_PI);
        foreach ($data as $xi) {
            $t = (2.5 - $xi) / $h;
            $sum += exp(-$t * $t / 2.0) / $sqrt2pi;
        }
        $expected = $sum / ($n * $h);
        $this->assertEqualsWithDelta($expected, $density, 1e-10);
    }

    public function test_kde_all_kernels(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];

        foreach (KdeKernel::cases() as $kernel) {
            $f = Stat::kde($data, 1.0, $kernel);
            $this->assertIsCallable($f, "Kernel '{$kernel->value}' should return a callable");
            $density = $f(3.0);
            $this->assertGreaterThanOrEqual(0, $density, "Kernel '{$kernel->value}' density should be >= 0");
        }
    }

    public function test_kde_cumulative(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $F = Stat::kde($data, 1.0, KdeKernel::Normal, cumulative: true);
        $this->assertIsCallable($F);

        // CDF should be monotonically non-decreasing
        $prev = $F(-100.0);
        foreach ([-10.0, 0.0, 1.0, 2.0, 3.0, 4.0, 5.0, 10.0, 100.0] as $x) {
            $current = $F($x);
            $this->assertGreaterThanOrEqual($prev, $current, "CDF should be non-decreasing at x=$x");
            $prev = $current;
        }

        // Approaches 0 far left and 1 far right
        $this->assertEqualsWithDelta(0.0, $F(-100.0), 0.01);
        $this->assertEqualsWithDelta(1.0, $F(100.0), 0.01);
    }

    public function test_kde_aliases(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $x = 3.0;

        // gauss == normal
        $f1 = Stat::kde($data, 1.0, KdeKernel::Gauss);
        $f2 = Stat::kde($data, 1.0, KdeKernel::Normal);
        $this->assertEqualsWithDelta($f1($x), $f2($x), 1e-15);

        // uniform == rectangular
        $f1 = Stat::kde($data, 1.0, KdeKernel::Uniform);
        $f2 = Stat::kde($data, 1.0, KdeKernel::Rectangular);
        $this->assertEqualsWithDelta($f1($x), $f2($x), 1e-15);

        // epanechnikov == parabolic
        $f1 = Stat::kde($data, 1.0, KdeKernel::Epanechnikov);
        $f2 = Stat::kde($data, 1.0, KdeKernel::Parabolic);
        $this->assertEqualsWithDelta($f1($x), $f2($x), 1e-15);

        // biweight == quartic
        $f1 = Stat::kde($data, 1.0, KdeKernel::Biweight);
        $f2 = Stat::kde($data, 1.0, KdeKernel::Quartic);
        $this->assertEqualsWithDelta($f1($x), $f2($x), 1e-15);
    }

    public function test_kde_empty_data(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kde([], 1.0);
    }

    public function test_kde_invalid_bandwidth(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kde([1.0, 2.0], 0.0);
    }

    public function test_kde_invalid_bandwidth_negative(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kde([1.0, 2.0], -1.0);
    }

    public function test_kde_random_returns_callable(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $sampler = Stat::kdeRandom($data, 1.0);
        $this->assertIsCallable($sampler);

        $value = $sampler();
        $this->assertIsFloat($value);
    }

    public function test_kde_random_all_kernels(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];

        foreach (KdeKernel::cases() as $kernel) {
            $sampler = Stat::kdeRandom($data, 1.0, $kernel, seed: 42);
            $this->assertIsCallable($sampler, "Kernel '{$kernel->value}' should return a callable");
            $value = $sampler();
            $this->assertIsFloat($value, "Kernel '{$kernel->value}' should return a float");
        }
    }

    public function test_kde_random_seed_reproducibility(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];

        $sampler1 = Stat::kdeRandom($data, 1.0, KdeKernel::Normal, seed: 123);
        $values1 = [];
        for ($i = 0; $i < 10; $i++) {
            $values1[] = $sampler1();
        }

        $sampler2 = Stat::kdeRandom($data, 1.0, KdeKernel::Normal, seed: 123);
        $values2 = [];
        for ($i = 0; $i < 10; $i++) {
            $values2[] = $sampler2();
        }

        $this->assertSame($values1, $values2);
    }

    public function test_kde_random_aliases(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $aliasPairs = [
            [KdeKernel::Gauss, KdeKernel::Normal],
            [KdeKernel::Uniform, KdeKernel::Rectangular],
            [KdeKernel::Epanechnikov, KdeKernel::Parabolic],
            [KdeKernel::Biweight, KdeKernel::Quartic],
        ];

        foreach ($aliasPairs as [$alias, $canonical]) {
            $sampler1 = Stat::kdeRandom($data, 1.0, $alias, seed: 42);
            $values1 = [];
            for ($i = 0; $i < 5; $i++) {
                $values1[] = $sampler1();
            }

            $sampler2 = Stat::kdeRandom($data, 1.0, $canonical, seed: 42);
            $values2 = [];
            for ($i = 0; $i < 5; $i++) {
                $values2[] = $sampler2();
            }

            $this->assertSame(
                $values1,
                $values2,
                "Alias '{$alias->value}' should produce same results as '{$canonical->value}'",
            );
        }
    }

    public function test_kde_random_known_output(): void
    {
        $data = [-2.1, -1.3, -0.4, 1.9, 5.1, 6.2];
        $rand = Stat::kdeRandom($data, 1.5, seed: 8675309);
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = round($rand(), 1);
        }

        $this->assertSame(
            [2.5, 3.3, -1.8, 7.3, -2.1, 4.6, 4.4, 5.9, -3.2, -1.6],
            $results,
        );
    }

    public function test_kde_random_statistical_properties(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $dataMean = 3.0;
        $sampler = Stat::kdeRandom($data, 0.5, KdeKernel::Normal, seed: 42);

        $sum = 0.0;
        $n = 10000;
        for ($i = 0; $i < $n; $i++) {
            $sum += $sampler();
        }
        $sampleMean = $sum / $n;

        $this->assertEqualsWithDelta($dataMean, $sampleMean, 0.15);
    }

    public function test_kde_random_empty_data(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kdeRandom([], 1.0);
    }

    public function test_kde_random_invalid_bandwidth(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::kdeRandom([1.0, 2.0], 0.0);
    }

    public function test_covariance_non_numeric_x_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        // true passes mean()'s string filter and array_sum without warnings,
        // but is_numeric(true) returns false, triggering the loop guard
        Stat::covariance([true, 1, 2], [3, 4, 5]); // @phpstan-ignore argument.type
    }

    public function test_covariance_non_numeric_y_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::covariance([1, 2, 3], [true, 4, 5]); // @phpstan-ignore argument.type
    }

    public function test_kde_cumulative_bounded_kernels(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        $boundedKernels = [
            KdeKernel::Triangular,
            KdeKernel::Parabolic,
            KdeKernel::Rectangular,
            KdeKernel::Quartic,
            KdeKernel::Triweight,
            KdeKernel::Cosine,
        ];

        foreach ($boundedKernels as $kernel) {
            $F = Stat::kde($data, 1.0, $kernel, cumulative: true);
            $this->assertIsCallable($F);

            // CDF must be monotonically non-decreasing
            $prev = $F(-100.0);
            foreach ([0.0, 1.0, 3.0, 5.0, 100.0] as $x) {
                $current = $F($x);
                $this->assertGreaterThanOrEqual(
                    $prev,
                    $current,
                    "CDF ({$kernel->value}) should be non-decreasing at x=$x",
                );
                $prev = $current;
            }

            // Boundary behaviour
            $this->assertEqualsWithDelta(0.0, $F(-100.0), 0.01);
            $this->assertEqualsWithDelta(1.0, $F(100.0), 0.01);
        }
    }

    public function test_kde_random_quartic_covers_small_p(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        // Generate enough samples so the rare p < 0.0106 branch is hit
        $sampler = Stat::kdeRandom($data, 1.0, KdeKernel::Quartic, seed: 42);
        for ($i = 0; $i < 500; $i++) {
            $value = $sampler();
            $this->assertIsFloat($value);
        }
    }

    public function test_kde_random_triweight_covers_both_signs(): void
    {
        $data = [1.0, 2.0, 3.0, 4.0, 5.0];
        // Generate enough samples so both p <= 0.5 and p > 0.5 branches are hit
        $sampler = Stat::kdeRandom($data, 1.0, KdeKernel::Triweight, seed: 42);
        for ($i = 0; $i < 500; $i++) {
            $value = $sampler();
            $this->assertIsFloat($value);
        }
    }

    // --- percentile ---

    public function test_percentile_median_matches(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $p50 = Stat::percentile($data, 50);
        $this->assertEqualsWithDelta(Stat::median($data), $p50, 1e-10);
    }

    public function test_percentile_quartiles(): void
    {
        $data = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20];
        $q1 = Stat::percentile($data, 25);
        $q3 = Stat::percentile($data, 75);
        $this->assertEqualsWithDelta(Stat::firstQuartile($data), $q1, 1e-10);
        $this->assertEqualsWithDelta(Stat::thirdQuartile($data), $q3, 1e-10);
    }

    public function test_percentile_boundaries(): void
    {
        $data = [10, 20, 30, 40, 50];
        $this->assertEqualsWithDelta(10.0, Stat::percentile($data, 0), 1e-10);
        $this->assertEqualsWithDelta(50.0, Stat::percentile($data, 100), 1e-10);
    }

    public function test_percentile_rounding(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $result = Stat::percentile($data, 33, 2);
        $this->assertEquals(round($result, 2), $result);
    }

    public function test_percentile_too_few_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::percentile([1], 50);
    }

    public function test_percentile_out_of_range_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::percentile([1, 2, 3], 101);
    }

    public function test_percentile_negative_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::percentile([1, 2, 3], -1);
    }

    // --- coefficientOfVariation ---

    public function test_coefficient_of_variation(): void
    {
        $data = [10, 20, 30, 40, 50];
        $expected = (Stat::stdev($data) / abs((float) Stat::mean($data))) * 100;
        $this->assertEqualsWithDelta($expected, Stat::coefficientOfVariation($data), 1e-10);
    }

    public function test_coefficient_of_variation_population(): void
    {
        $data = [10, 20, 30, 40, 50];
        $expected = (Stat::pstdev($data) / abs((float) Stat::mean($data))) * 100;
        $this->assertEqualsWithDelta($expected, Stat::coefficientOfVariation($data, population: true), 1e-10);
    }

    public function test_coefficient_of_variation_rounding(): void
    {
        $data = [10, 20, 30, 40, 50];
        $result = Stat::coefficientOfVariation($data, 2);
        $this->assertEquals(round($result, 2), $result);
    }

    public function test_coefficient_of_variation_low_dispersion(): void
    {
        // Nearly identical values → low CV
        $data = [100, 100.1, 99.9, 100.2, 99.8];
        $cv = Stat::coefficientOfVariation($data);
        $this->assertLessThan(1.0, $cv);
    }

    public function test_coefficient_of_variation_zero_mean_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::coefficientOfVariation([-1, 0, 1]);
    }

    public function test_coefficient_of_variation_negative_mean(): void
    {
        $data = [-10, -20, -30];
        // Should use abs(mean), so CV is still positive
        $cv = Stat::coefficientOfVariation($data);
        $this->assertGreaterThan(0, $cv);
    }

    public function test_coefficient_of_variation_too_few_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::coefficientOfVariation([5]);
    }

    // --- trimmedMean ---

    public function test_trimmed_mean_basic(): void
    {
        // [1, 2, 3, 4, 5, 6, 7, 8, 9, 100] with 10% trim
        // removes 1 from each side → mean of [2, 3, 4, 5, 6, 7, 8, 9]
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 100];
        $result = Stat::trimmedMean($data, 0.1);
        $this->assertEqualsWithDelta(5.5, $result, 1e-10);
    }

    public function test_trimmed_mean_zero_trim_equals_mean(): void
    {
        $data = [1, 2, 3, 4, 5];
        $this->assertEqualsWithDelta(
            (float) Stat::mean($data),
            Stat::trimmedMean($data, 0.0),
            1e-10,
        );
    }

    public function test_trimmed_mean_with_rounding(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $result = Stat::trimmedMean($data, 0.2, 2);
        $this->assertEquals(round($result, 2), $result);
    }

    public function test_trimmed_mean_removes_outliers(): void
    {
        // Outliers at both ends; trimmed mean should be close to the "clean" mean
        $data = [-1000, 2, 3, 4, 5, 6, 7, 8, 9, 1000];
        $trimmed = Stat::trimmedMean($data, 0.1);
        $this->assertEqualsWithDelta(5.5, $trimmed, 1e-10);
    }

    public function test_trimmed_mean_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::trimmedMean([]);
    }

    public function test_trimmed_mean_proportion_too_high_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::trimmedMean([1, 2, 3], 0.5);
    }

    public function test_trimmed_mean_negative_proportion_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::trimmedMean([1, 2, 3], -0.1);
    }

    // --- weightedMedian ---

    public function test_weighted_median_basic(): void
    {
        // Values: [1, 2, 3], Weights: [1, 1, 1] → equal weights, same as regular median
        $this->assertEqualsWithDelta(2.0, Stat::weightedMedian([1, 2, 3], [1, 1, 1]), 1e-10);
    }

    public function test_weighted_median_skewed_weights(): void
    {
        // Heavy weight on 3 pulls the weighted median to 3
        $this->assertEqualsWithDelta(3.0, Stat::weightedMedian([1, 2, 3], [1, 1, 10]), 1e-10);
    }

    public function test_weighted_median_unsorted_data(): void
    {
        // Data not pre-sorted — method should sort internally
        $this->assertEqualsWithDelta(
            Stat::weightedMedian([1, 2, 3], [1, 1, 1]),
            Stat::weightedMedian([3, 1, 2], [1, 1, 1]),
            1e-10,
        );
    }

    public function test_weighted_median_interpolation_at_midpoint(): void
    {
        // Cumulative weight hits exactly 50% between two values → average them
        // Values: [1, 2, 3, 4], Weights: [1, 1, 1, 1] → total=4, half=2
        // After [1,2]: cumulative = 2 = half → average of 2 and 3 = 2.5
        $this->assertEqualsWithDelta(2.5, Stat::weightedMedian([1, 2, 3, 4], [1, 1, 1, 1]), 1e-10);
    }

    public function test_weighted_median_single_element(): void
    {
        $this->assertEqualsWithDelta(42.0, Stat::weightedMedian([42], [5]), 1e-10);
    }

    public function test_weighted_median_with_rounding(): void
    {
        $result = Stat::weightedMedian([1, 2, 3, 4], [1, 1, 1, 1], 1);
        $this->assertEquals(round($result, 1), $result);
    }

    public function test_weighted_median_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::weightedMedian([], []);
    }

    public function test_weighted_median_length_mismatch_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::weightedMedian([1, 2, 3], [1, 2]);
    }

    public function test_weighted_median_negative_weight_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::weightedMedian([1, 2, 3], [1, -1, 1]);
    }

    public function test_weighted_median_zero_weight_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::weightedMedian([1, 2, 3], [1, 0, 1]);
    }

    // --- sem ---

    public function test_sem(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $expected = Stat::stdev($data) / sqrt(Stat::count($data));
        $this->assertEqualsWithDelta($expected, Stat::sem($data), 1e-10);
    }

    public function test_sem_with_rounding(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $result = Stat::sem($data, 4);
        $this->assertEquals(round($result, 4), $result);
    }

    public function test_sem_decreases_with_larger_sample(): void
    {
        $small = [1, 2, 3, 4, 5];
        $large = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5];
        $this->assertGreaterThan(Stat::sem($large), Stat::sem($small));
    }

    public function test_sem_too_few_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::sem([5]);
    }

    // --- meanAbsoluteDeviation ---

    public function test_mean_absolute_deviation(): void
    {
        // [1, 2, 3, 4, 5] → mean=3, deviations=[2,1,0,1,2], MAD=6/5=1.2
        $this->assertEqualsWithDelta(1.2, Stat::meanAbsoluteDeviation([1, 2, 3, 4, 5]), 1e-10);
    }

    public function test_mean_absolute_deviation_single_element(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::meanAbsoluteDeviation([42]), 1e-10);
    }

    public function test_mean_absolute_deviation_identical_values(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::meanAbsoluteDeviation([5, 5, 5, 5]), 1e-10);
    }

    public function test_mean_absolute_deviation_with_rounding(): void
    {
        $result = Stat::meanAbsoluteDeviation([1, 2, 3, 4, 5], 2);
        $this->assertEquals(round($result, 2), $result);
    }

    public function test_mean_absolute_deviation_less_than_stdev(): void
    {
        // MAD is always <= stdev for any dataset
        $data = [1, 2, 3, 10, 100];
        $this->assertLessThanOrEqual(Stat::stdev($data), Stat::meanAbsoluteDeviation($data));
    }

    public function test_mean_absolute_deviation_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::meanAbsoluteDeviation([]);
    }

    // --- medianAbsoluteDeviation ---

    public function test_median_absolute_deviation(): void
    {
        // [1, 2, 3, 4, 5] → median=3, deviations=[2,1,0,1,2], median of deviations=1
        $this->assertEqualsWithDelta(1.0, Stat::medianAbsoluteDeviation([1, 2, 3, 4, 5]), 1e-10);
    }

    public function test_median_absolute_deviation_with_outlier(): void
    {
        // MAD should be resistant to the outlier
        $clean = [1, 2, 3, 4, 5];
        $withOutlier = [1, 2, 3, 4, 1000];
        // median of clean = 3, deviations = [2,1,0,1,2], MAD = 1
        // median of withOutlier = 3, deviations = [2,1,0,1,997], MAD = 1
        $this->assertEqualsWithDelta(
            Stat::medianAbsoluteDeviation($clean),
            Stat::medianAbsoluteDeviation($withOutlier),
            1e-10,
        );
    }

    public function test_median_absolute_deviation_single_element(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::medianAbsoluteDeviation([42]), 1e-10);
    }

    public function test_median_absolute_deviation_identical_values(): void
    {
        $this->assertEqualsWithDelta(0.0, Stat::medianAbsoluteDeviation([5, 5, 5, 5]), 1e-10);
    }

    public function test_median_absolute_deviation_with_rounding(): void
    {
        $result = Stat::medianAbsoluteDeviation([1, 2, 3, 4, 5, 6, 7, 8], 3);
        $this->assertEquals(round($result, 3), $result);
    }

    public function test_median_absolute_deviation_empty_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::medianAbsoluteDeviation([]);
    }

    // --- zscores ---

    public function test_zscores(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $zscores = Stat::zscores($data);
        $mean = (float) Stat::mean($data);
        $stdev = Stat::stdev($data);

        $this->assertCount(count($data), $zscores);
        foreach ($data as $i => $value) {
            $this->assertEqualsWithDelta(($value - $mean) / $stdev, $zscores[$i], 1e-10);
        }
    }

    public function test_zscores_sum_to_zero(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $zscores = Stat::zscores($data);
        $this->assertEqualsWithDelta(0.0, array_sum($zscores), 1e-10);
    }

    public function test_zscores_with_rounding(): void
    {
        $data = [2, 4, 4, 4, 5, 5, 7, 9];
        $zscores = Stat::zscores($data, 2);
        foreach ($zscores as $z) {
            $this->assertEquals(round($z, 2), $z);
        }
    }

    public function test_zscores_identical_values_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::zscores([5, 5, 5, 5]);
    }

    public function test_zscores_too_few_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::zscores([5]);
    }

    // --- outliers ---

    public function test_outliers_detects_extreme_values(): void
    {
        $data = [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 100];
        $outliers = Stat::outliers($data);
        $this->assertContains(100, $outliers);
    }

    public function test_outliers_no_outliers(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $outliers = Stat::outliers($data);
        $this->assertEmpty($outliers);
    }

    public function test_outliers_custom_threshold(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        // With a very low threshold, more values are flagged
        $outliers = Stat::outliers($data, 1.0);
        $this->assertNotEmpty($outliers);
    }

    public function test_outliers_identical_values_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::outliers([5, 5, 5, 5]);
    }

    // --- iqrOutliers ---

    public function test_iqr_outliers_detects_extreme_values(): void
    {
        // Ski downhill race times (seconds): most between 108-118, one DNF-like 200, one impossibly fast 50
        $times = [110.2, 112.5, 108.9, 115.3, 111.7, 114.0, 109.8, 113.6, 200.0, 50.0];
        $outliers = Stat::iqrOutliers($times);
        $this->assertContains(200.0, $outliers);
        $this->assertContains(50.0, $outliers);
        // Normal times should not be flagged
        $this->assertNotContains(112.5, $outliers);
    }

    public function test_iqr_outliers_no_outliers(): void
    {
        $data = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
        $outliers = Stat::iqrOutliers($data);
        $this->assertEmpty($outliers);
    }

    public function test_iqr_outliers_custom_factor(): void
    {
        // With factor 3.0 (extreme outliers only), fewer values flagged
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 30];
        $mild = Stat::iqrOutliers($data, 1.5);
        $extreme = Stat::iqrOutliers($data, 3.0);
        $this->assertGreaterThanOrEqual(count($extreme), count($mild));
    }

    public function test_iqr_outliers_identical_values(): void
    {
        // IQR = 0, so any different value is an outlier... but all values are the same
        $data = [5, 5, 5, 5, 5];
        $outliers = Stat::iqrOutliers($data);
        $this->assertEmpty($outliers);
    }

    public function test_iqr_outliers_with_negative_values(): void
    {
        $data = [-100, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 100];
        $outliers = Stat::iqrOutliers($data);
        $this->assertContains(-100, $outliers);
        $this->assertContains(100, $outliers);
    }

    public function test_iqr_outliers_too_few_data_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        Stat::iqrOutliers([5]);
    }
}
