<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\NormalDist;
use HiFolks\Statistics\StudentT;
use PHPUnit\Framework\TestCase;

class StudentTTest extends TestCase
{
    // --- Constructor ---

    public function test_constructor_valid_df(): void
    {
        $t = new StudentT(5);
        $this->assertEquals(5.0, $t->getDegreesOfFreedom());
    }

    public function test_constructor_fractional_df(): void
    {
        $t = new StudentT(2.5);
        $this->assertEquals(2.5, $t->getDegreesOfFreedom());
    }

    public function test_constructor_zero_df_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        new StudentT(0);
    }

    public function test_constructor_negative_df_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        new StudentT(-1);
    }

    // --- PDF ---

    public function test_pdf_df1_cauchy(): void
    {
        // For df=1, the t-distribution is the Cauchy distribution
        // pdf(0) = 1/(pi) ≈ 0.31831
        $t = new StudentT(1);
        $this->assertEqualsWithDelta(1 / M_PI, $t->pdf(0), 1e-5);
    }

    public function test_pdf_df5(): void
    {
        // Known value from scipy: scipy.stats.t.pdf(0, 5) ≈ 0.37960669
        $t = new StudentT(5);
        $this->assertEqualsWithDelta(0.37961, $t->pdfRounded(0, 5), 1e-4);
    }

    public function test_pdf_df30(): void
    {
        // For df=30, pdf(0) should be close to standard normal pdf(0) ≈ 0.39894
        $t = new StudentT(30);
        $normal = new NormalDist(0, 1);
        $this->assertEqualsWithDelta($normal->pdf(0), $t->pdf(0), 0.01);
    }

    public function test_pdf_symmetry(): void
    {
        $t = new StudentT(5);
        $this->assertEqualsWithDelta($t->pdf(2.0), $t->pdf(-2.0), 1e-10);
        $this->assertEqualsWithDelta($t->pdf(0.5), $t->pdf(-0.5), 1e-10);
    }

    public function test_pdf_tails(): void
    {
        // PDF should decrease away from center
        $t = new StudentT(10);
        $this->assertGreaterThan($t->pdf(1), $t->pdf(0));
        $this->assertGreaterThan($t->pdf(2), $t->pdf(1));
    }

    public function test_pdf_rounded(): void
    {
        $t = new StudentT(5);
        $this->assertEquals(0.38, $t->pdfRounded(0, 2));
    }

    // --- CDF ---

    public function test_cdf_at_zero(): void
    {
        // cdf(0) = 0.5 for all df (by symmetry)
        foreach ([1, 2, 5, 10, 30, 100] as $df) {
            $t = new StudentT($df);
            $this->assertEqualsWithDelta(0.5, $t->cdf(0), 1e-6, "cdf(0) should be 0.5 for df=$df");
        }
    }

    public function test_cdf_df1_cauchy(): void
    {
        // For df=1 (Cauchy): cdf(1) = 0.75, cdf(-1) = 0.25
        $t = new StudentT(1);
        $this->assertEqualsWithDelta(0.75, $t->cdf(1), 1e-4);
        $this->assertEqualsWithDelta(0.25, $t->cdf(-1), 1e-4);
    }

    public function test_cdf_df5_known_values(): void
    {
        // scipy.stats.t.cdf(2.0, 5) ≈ 0.94874
        $t = new StudentT(5);
        $this->assertEqualsWithDelta(0.94874, $t->cdf(2.0), 1e-3);
    }

    public function test_cdf_monotonicity(): void
    {
        $t = new StudentT(10);
        $prev = 0.0;
        foreach ([-3, -2, -1, 0, 1, 2, 3] as $x) {
            $val = $t->cdf($x);
            $this->assertGreaterThan($prev, $val);
            $prev = $val;
        }
    }

    public function test_cdf_converges_to_normal_for_large_df(): void
    {
        $t = new StudentT(1000);
        $normal = new NormalDist(0, 1);
        foreach ([-2.0, -1.0, 0.0, 1.0, 2.0] as $x) {
            $this->assertEqualsWithDelta(
                $normal->cdf($x),
                $t->cdf($x),
                0.005,
                "t(1000) should approximate normal at x=$x",
            );
        }
    }

    public function test_cdf_rounded(): void
    {
        $t = new StudentT(5);
        $this->assertEquals(0.949, $t->cdfRounded(2.0, 3));
    }

    // --- invCdf ---

    public function test_inv_cdf_round_trip(): void
    {
        $t = new StudentT(10);
        foreach ([0.05, 0.1, 0.25, 0.5, 0.75, 0.9, 0.95] as $p) {
            $this->assertEqualsWithDelta($p, $t->cdf($t->invCdf($p)), 1e-6, "round-trip for p=$p");
        }
    }

    public function test_inv_cdf_symmetry(): void
    {
        $t = new StudentT(10);
        foreach ([0.1, 0.25, 0.05] as $p) {
            $this->assertEqualsWithDelta(
                -$t->invCdf($p),
                $t->invCdf(1 - $p),
                1e-6,
                "invCdf(p) should equal -invCdf(1-p) for p=$p",
            );
        }
    }

    public function test_inv_cdf_median(): void
    {
        // invCdf(0.5) = 0 for all df
        foreach ([1, 5, 30] as $df) {
            $t = new StudentT($df);
            $this->assertEqualsWithDelta(0.0, $t->invCdf(0.5), 1e-6, "invCdf(0.5)=0 for df=$df");
        }
    }

    public function test_inv_cdf_throws_for_p_zero(): void
    {
        $t = new StudentT(5);
        $this->expectException(InvalidDataInputException::class);
        $t->invCdf(0.0);
    }

    public function test_inv_cdf_throws_for_p_one(): void
    {
        $t = new StudentT(5);
        $this->expectException(InvalidDataInputException::class);
        $t->invCdf(1.0);
    }

    public function test_inv_cdf_rounded(): void
    {
        $t = new StudentT(10);
        $val = $t->invCdfRounded(0.975, 3);
        $this->assertEqualsWithDelta(2.228, $val, 0.001);
    }

    public function test_inv_cdf_extreme_tail(): void
    {
        // Very small p with df=1 (Cauchy) — the normal approximation
        // initial guess lands where pdf is near zero, hitting the fpx < 1e-15 guard
        $t = new StudentT(1);
        $val = $t->invCdf(1e-10);
        $this->assertLessThan(-1000.0, $val);
    }

    // --- Coverage: logGamma reflection formula (x < 0.5) ---

    public function test_pdf_fractional_df_triggers_loggamma_reflection(): void
    {
        // df=0.5 => logGamma(0.25) is called, which triggers the
        // reflection branch (x < 0.5) in logGamma
        $t = new StudentT(0.5);
        $this->assertGreaterThan(0.0, $t->pdf(0));
        // Also verify cdf is still valid
        $this->assertEqualsWithDelta(0.5, $t->cdf(0), 1e-6);
    }

    // --- Coverage: regularizedIncompleteBeta edge cases ---

    public function test_cdf_very_large_t_value(): void
    {
        // Very large t => x = df/(df+t^2) ≈ 0, approaching the x===0 branch
        $t = new StudentT(5);
        $this->assertEqualsWithDelta(1.0, $t->cdf(1e8), 1e-6);
    }

    public function test_cdf_negative_very_large_t_value(): void
    {
        // Very negative t => cdf near 0
        $t = new StudentT(5);
        $this->assertEqualsWithDelta(0.0, $t->cdf(-1e8), 1e-6);
    }

    public function test_cdf_df1_wide_range(): void
    {
        // df=1 (Cauchy) with various t-values to exercise different
        // paths in the incomplete beta (symmetry flip, CF convergence)
        $t = new StudentT(1);
        // Known Cauchy quantiles
        $this->assertEqualsWithDelta(0.5, $t->cdf(0), 1e-6);
        $this->assertEqualsWithDelta(0.75, $t->cdf(1), 1e-4);
        $this->assertEqualsWithDelta(1.0 - 0.75, $t->cdf(-1), 1e-4);
        // Extreme tails
        $this->assertGreaterThan(0.99, $t->cdf(100));
        $this->assertLessThan(0.01, $t->cdf(-100));
    }

    // --- Coverage: incompleteBetaCf tiny-value guards ---

    public function test_cdf_df2_known_values(): void
    {
        // df=2: cdf(t) = 0.5 + t/(2*sqrt(2+t^2))
        // This exercises the CF with a=1.0, b=0.5, which can produce
        // near-zero denominators triggering the tiny guards
        $t = new StudentT(2);
        foreach ([0.5, 1.0, 2.0, 5.0] as $tv) {
            $expected = 0.5 + $tv / (2 * sqrt(2 + $tv * $tv));
            $this->assertEqualsWithDelta($expected, $t->cdf($tv), 1e-5, "df=2, t=$tv");
        }
    }

    public function test_cdf_very_high_df(): void
    {
        // Very high df pushes x close to 1 in regularizedIncompleteBeta,
        // forcing the symmetry flip branch
        $t = new StudentT(10000);
        $normal = new NormalDist(0, 1);
        $this->assertEqualsWithDelta($normal->cdf(1.96), $t->cdf(1.96), 0.001);
    }

    public function test_cdf_small_t_many_df_values(): void
    {
        // Small t with various df to exercise different a/b ratios
        // in the continued fraction, hitting different convergence paths
        foreach ([1, 2, 3, 4, 5, 10, 50, 200] as $df) {
            $t = new StudentT($df);
            // cdf should be between 0.5 and 1 for positive t
            $val = $t->cdf(0.1);
            $this->assertGreaterThan(0.5, $val, "cdf(0.1) > 0.5 for df=$df");
            $this->assertLessThan(1.0, $val, "cdf(0.1) < 1.0 for df=$df");
        }
    }

    public function test_cdf_symmetry_identity(): void
    {
        // cdf(t) + cdf(-t) = 1 for all t and df
        // This exercises both branches (t >= 0 and t < 0) of the CDF
        $t = new StudentT(3);
        foreach ([0.1, 0.5, 1.0, 2.0, 5.0, 10.0] as $tv) {
            $this->assertEqualsWithDelta(
                1.0,
                $t->cdf($tv) + $t->cdf(-$tv),
                1e-10,
                "cdf($tv) + cdf(-$tv) should equal 1",
            );
        }
    }
}
