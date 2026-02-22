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
}
