<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\NormalDist;
use PHPUnit\Framework\TestCase;

class NormalDistTest extends TestCase
{
    public function test_init_normal_dist(): void
    {
        $nd = new NormalDist(1060, 195);
        $this->assertEquals(1060, $nd->getMean());
        $this->assertEquals(195, $nd->getSigma());
    }

    public function test_can_calculate_normal_dist_cdf(): void
    {
        $nd = new NormalDist(1060, 195);
        $this->assertEquals(0.184, round($nd->cdf(1200 + 0.5) - $nd->cdf(1100 - 0.5), 3));
    }

    public function test_can_calculate_normal_dist_pdf(): void
    {
        $nd = new NormalDist(10, 2);
        $this->assertEquals(0.121, $nd->pdfRounded(12, 3));
        $this->assertEquals(0.12, $nd->pdfRounded(12, 2));
    }

    public function test_median(): void
    {
        $nd = new NormalDist(100, 15);
        $this->assertEquals(100.0, $nd->getMedian());
        $this->assertEquals(100.0, $nd->getMedianRounded(2));

        // Median always equals mean for a normal distribution
        $nd2 = new NormalDist(5.123, 2.5);
        $this->assertEquals($nd2->getMean(), $nd2->getMedian());
    }

    public function test_median_from_samples(): void
    {
        $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
        $nd = NormalDist::fromSamples($samples);
        $this->assertEquals($nd->getMeanRounded(5), $nd->getMedianRounded(5));
        $this->assertEquals(2.71667, $nd->getMedianRounded(5));
    }

    public function test_mode(): void
    {
        $nd = new NormalDist(100, 15);
        $this->assertEquals(100.0, $nd->getMode());
        $this->assertEquals(100.0, $nd->getModeRounded(2));

        // Mode always equals mean for a normal distribution
        $nd2 = new NormalDist(5.123, 2.5);
        $this->assertEquals($nd2->getMean(), $nd2->getMode());
    }

    public function test_mode_from_samples(): void
    {
        $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
        $nd = NormalDist::fromSamples($samples);
        $this->assertEquals($nd->getMeanRounded(5), $nd->getModeRounded(5));
        $this->assertEquals(2.71667, $nd->getModeRounded(5));
    }

    public function test_variance(): void
    {
        $nd = new NormalDist(0, 1);
        $this->assertEquals(1.0, $nd->getVariance());

        $nd2 = new NormalDist(10, 2);
        $this->assertEquals(4.0, $nd2->getVariance());

        $nd3 = new NormalDist(100, 15);
        $this->assertEquals(225.0, $nd3->getVariance());
        $this->assertEquals(225.0, $nd3->getVarianceRounded(2));
    }

    public function test_variance_from_samples(): void
    {
        $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
        $nd = NormalDist::fromSamples($samples);
        $this->assertEquals(0.25767, $nd->getVarianceRounded(5));
    }

    public function test_load_normal_dist_from_samples(): void
    {
        $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
        $normalDist = NormalDist::fromSamples($samples);
        $this->assertEquals(2.71667, $normalDist->getMeanRounded(5));
        $this->assertEquals(0.50761, $normalDist->getSigmaRounded(5));
    }

    public function test_add_to_normal_dist(): void
    {
        $birth_weights = NormalDist::fromSamples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5]);
        $drug_effects = new NormalDist(0.4, 0.15);
        $combined = $birth_weights->add($drug_effects);
        $this->assertEquals(3.1, $combined->getMeanRounded(1));
        $this->assertEquals(0.5, $combined->getSigmaRounded(1));
        $this->assertEquals(2.71667, $birth_weights->getMeanRounded(5));
        $this->assertEquals(0.50761, $birth_weights->getSigmaRounded(5));
    }

    public function test_multiply_normal_dist(): void
    {
        $tempFebruaryCelsius = new NormalDist(5, 2.5);
        $tempFebFahrenheit = $tempFebruaryCelsius->multiply(9 / 5)->add(32);
        $this->assertEquals(41.0, $tempFebFahrenheit->getMeanRounded(1));
        $this->assertEquals(4.5, $tempFebFahrenheit->getSigmaRounded(1));
        $this->assertEquals(5.0, $tempFebruaryCelsius->getMeanRounded(1));
        $this->assertEquals(2.5, $tempFebruaryCelsius->getSigmaRounded(1));
    }

    public function test_subtract_constant_from_normal_dist(): void
    {
        $nd = new NormalDist(100, 15);
        $result = $nd->subtract(32);
        $this->assertEquals(68.0, $result->getMean());
        $this->assertEquals(15.0, $result->getSigma());
        // Original unchanged
        $this->assertEquals(100.0, $nd->getMean());
    }

    public function test_subtract_normal_dist(): void
    {
        $n1 = new NormalDist(10, 3);
        $n2 = new NormalDist(4, 2);
        $result = $n1->subtract($n2);
        $this->assertEquals(6.0, $result->getMean());
        // sigma = sqrt(3^2 + 2^2) = sqrt(13)
        $this->assertEqualsWithDelta(sqrt(13), $result->getSigma(), 1e-10);
    }

    public function test_divide_normal_dist(): void
    {
        // Fahrenheit to Celsius: (F - 32) / (9/5)
        $tempFahrenheit = new NormalDist(41, 4.5);
        $tempCelsius = $tempFahrenheit->subtract(32)->divide(9 / 5);
        $this->assertEquals(5.0, $tempCelsius->getMeanRounded(1));
        $this->assertEquals(2.5, $tempCelsius->getSigmaRounded(1));
    }

    public function test_divide_preserves_original(): void
    {
        $nd = new NormalDist(100, 20);
        $result = $nd->divide(2);
        $this->assertEquals(50.0, $result->getMean());
        $this->assertEquals(10.0, $result->getSigma());
        // Original unchanged
        $this->assertEquals(100.0, $nd->getMean());
        $this->assertEquals(20.0, $nd->getSigma());
    }

    public function test_divide_by_zero_throws(): void
    {
        $nd = new NormalDist(100, 15);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->divide(0);
    }

    public function test_quantiles_default_quartiles(): void
    {
        $nd = new NormalDist(0, 1);
        $q = $nd->quantiles(); // default n=4
        $this->assertCount(3, $q);
        // Q1 ≈ -0.6745, Q2 = 0, Q3 ≈ 0.6745
        $this->assertEqualsWithDelta(-0.6745, $q[0], 1e-3);
        $this->assertEqualsWithDelta(0.0, $q[1], 1e-5);
        $this->assertEqualsWithDelta(0.6745, $q[2], 1e-3);
    }

    public function test_quantiles_deciles(): void
    {
        $nd = new NormalDist(100, 15);
        $q = $nd->quantiles(10); // deciles
        $this->assertCount(9, $q);
        // The 5th decile (median) should equal the mean
        $this->assertEqualsWithDelta(100.0, $q[4], 1e-5);
        // Symmetric: distance from mean should be equal for q[0] and q[8]
        $this->assertEqualsWithDelta(
            $q[4] - $q[0],
            $q[8] - $q[4],
            1e-5,
        );
    }

    public function test_quantiles_percentiles(): void
    {
        $nd = new NormalDist(0, 1);
        $q = $nd->quantiles(100);
        $this->assertCount(99, $q);
        // 50th percentile should be 0
        $this->assertEqualsWithDelta(0.0, $q[49], 1e-5);
    }

    public function test_quantiles_n_one_returns_empty(): void
    {
        $nd = new NormalDist(0, 1);
        $q = $nd->quantiles(1);
        $this->assertCount(0, $q);
    }

    public function test_quantiles_throws_for_invalid_n(): void
    {
        $nd = new NormalDist(0, 1);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->quantiles(0);
    }

    public function test_samples_count(): void
    {
        $nd = new NormalDist(100, 15);
        $samples = $nd->samples(1000);
        $this->assertCount(1000, $samples);
    }

    public function test_samples_statistical_properties(): void
    {
        // With enough samples, mean and stdev should approximate mu and sigma
        $nd = new NormalDist(50, 10);
        $samples = $nd->samples(10000, seed: 42);
        $sampleMean = array_sum($samples) / count($samples);
        $this->assertEqualsWithDelta(50, $sampleMean, 1.0);

        $reconstructed = NormalDist::fromSamples($samples);
        $this->assertEqualsWithDelta(10, $reconstructed->getSigma(), 1.0);
    }

    public function test_samples_seed_reproducibility(): void
    {
        $nd = new NormalDist(0, 1);
        $a = $nd->samples(100, seed: 123);
        $b = $nd->samples(100, seed: 123);
        $this->assertEquals($a, $b);
    }

    public function test_samples_throws_for_invalid_n(): void
    {
        $nd = new NormalDist(0, 1);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->samples(0);
    }

    public function test_zscore(): void
    {
        $nd = new NormalDist(100, 15);
        // Mean has z-score of 0
        $this->assertEquals(0.0, $nd->zscore(100));
        // One standard deviation above
        $this->assertEqualsWithDelta(1.0, $nd->zscore(115), 1e-10);
        // One standard deviation below
        $this->assertEqualsWithDelta(-1.0, $nd->zscore(85), 1e-10);
        // Two standard deviations above
        $this->assertEqualsWithDelta(2.0, $nd->zscore(130), 1e-10);
    }

    public function test_zscore_standard_normal(): void
    {
        // For standard normal, zscore(x) == x
        $nd = new NormalDist(0, 1);
        $this->assertEqualsWithDelta(1.5, $nd->zscore(1.5), 1e-10);
        $this->assertEqualsWithDelta(-2.3, $nd->zscore(-2.3), 1e-10);
    }

    public function test_zscore_rounded(): void
    {
        $nd = new NormalDist(10, 3);
        $this->assertEquals(1.333, $nd->zscoreRounded(14, 3));
    }

    public function test_zscore_throws_for_zero_sigma(): void
    {
        $nd = new NormalDist(5, 0);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->zscore(5);
    }

    public function test_overlap_identical_distributions(): void
    {
        $nd = new NormalDist(0, 1);
        // Identical distributions overlap completely
        $this->assertEqualsWithDelta(1.0, $nd->overlap($nd), 1e-5);
    }

    public function test_overlap_different_means(): void
    {
        // Python reference: NormalDist(2.4, 1.6).overlap(NormalDist(3.2, 2.0)) ≈ 0.8035
        $n1 = new NormalDist(2.4, 1.6);
        $n2 = new NormalDist(3.2, 2.0);
        $this->assertEqualsWithDelta(0.8035, $n1->overlapRounded($n2, 4), 1e-3);
    }

    public function test_overlap_equal_variances(): void
    {
        // Equal sigma, different means
        $n1 = new NormalDist(0, 1);
        $n2 = new NormalDist(1, 1);
        $overlap = $n1->overlap($n2);
        $this->assertGreaterThan(0.0, $overlap);
        $this->assertLessThan(1.0, $overlap);
        // Symmetric: order shouldn't matter
        $this->assertEqualsWithDelta($overlap, $n2->overlap($n1), 1e-10);
    }

    public function test_overlap_far_apart_distributions(): void
    {
        // Very far apart distributions should have near-zero overlap
        $n1 = new NormalDist(0, 1);
        $n2 = new NormalDist(100, 1);
        $this->assertEqualsWithDelta(0.0, $n1->overlap($n2), 1e-5);
    }

    public function test_overlap_is_symmetric(): void
    {
        $n1 = new NormalDist(5, 2);
        $n2 = new NormalDist(10, 3);
        $this->assertEqualsWithDelta(
            $n1->overlap($n2),
            $n2->overlap($n1),
            1e-10,
        );
    }

    public function test_inv_cdf_standard_normal(): void
    {
        $nd = new NormalDist(0, 1);
        // inv_cdf(0.5) should be 0 for standard normal
        $this->assertEqualsWithDelta(0.0, $nd->invCdf(0.5), 1e-5);
        // Known quantiles of standard normal
        $this->assertEqualsWithDelta(-1.64485, $nd->invCdfRounded(0.05, 5), 1e-4);
        $this->assertEqualsWithDelta(-1.28155, $nd->invCdfRounded(0.1, 5), 1e-4);
        $this->assertEqualsWithDelta(1.28155, $nd->invCdfRounded(0.9, 5), 1e-4);
        $this->assertEqualsWithDelta(1.64485, $nd->invCdfRounded(0.95, 5), 1e-4);
    }

    public function test_inv_cdf_custom_distribution(): void
    {
        $nd = new NormalDist(100, 15);
        // inv_cdf(0.5) should equal the mean
        $this->assertEqualsWithDelta(100.0, $nd->invCdf(0.5), 1e-5);
        // A round-trip: cdf(inv_cdf(p)) should equal p
        $this->assertEqualsWithDelta(0.25, $nd->cdf($nd->invCdf(0.25)), 1e-5);
        $this->assertEqualsWithDelta(0.75, $nd->cdf($nd->invCdf(0.75)), 1e-5);
        $this->assertEqualsWithDelta(0.99, $nd->cdf($nd->invCdf(0.99)), 1e-4);
    }

    public function test_inv_cdf_throws_for_invalid_p(): void
    {
        $nd = new NormalDist(0, 1);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->invCdf(0.0);
    }

    public function test_inv_cdf_throws_for_p_equals_one(): void
    {
        $nd = new NormalDist(0, 1);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $nd->invCdf(1.0);
    }

    public function test_inv_cdf_extreme_tails(): void
    {
        $nd = new NormalDist(0, 1);
        // Very low probability (lower tail)
        $this->assertEqualsWithDelta(-3.09023, $nd->invCdfRounded(0.001, 5), 1e-3);
        // Very high probability (upper tail)
        $this->assertEqualsWithDelta(3.09023, $nd->invCdfRounded(0.999, 5), 1e-3);
    }

    public function test_constructor_negative_sigma_throws(): void
    {
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        new NormalDist(0.0, -1.0);
    }

    public function test_from_samples_empty_throws(): void
    {
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        NormalDist::fromSamples([]);
    }

    public function test_cdf_rounded(): void
    {
        $nd = new NormalDist(0.0, 1.0);
        $this->assertEquals(0.5, $nd->cdfRounded(0.0));
        $this->assertEquals(0.841, $nd->cdfRounded(1.0));
        $this->assertEquals(0.84134, $nd->cdfRounded(1.0, 5));
    }

    public function test_overlap_zero_sigma_throws(): void
    {
        $a = new NormalDist(0.0, 0.0);
        $b = new NormalDist(1.0, 1.0);
        $this->expectException(\HiFolks\Statistics\Exception\InvalidDataInputException::class);
        $a->overlap($b);
    }
}
