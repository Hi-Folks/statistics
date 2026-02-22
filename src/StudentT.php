<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Exception\InvalidDataInputException;

class StudentT
{
    public function __construct(private readonly float $df)
    {
        if ($df <= 0) {
            throw new InvalidDataInputException('Degrees of freedom (df) must be greater than 0.');
        }
    }

    public function getDegreesOfFreedom(): float
    {
        return $this->df;
    }

    /**
     * Probability density function of the Student's t-distribution.
     */
    public function pdf(float $t): float
    {
        $df = $this->df;

        $logCoeff = self::logGamma(($df + 1) / 2) - self::logGamma($df / 2) - 0.5 * log($df * M_PI);
        $logBody = -(($df + 1) / 2) * log(1 + ($t * $t) / $df);

        return exp($logCoeff + $logBody);
    }

    public function pdfRounded(float $t, int $precision = 3): float
    {
        return round($this->pdf($t), $precision);
    }

    /**
     * Cumulative distribution function of the Student's t-distribution.
     *
     * Uses the regularized incomplete beta function.
     */
    public function cdf(float $t): float
    {
        $df = $this->df;
        $x = $df / ($df + $t * $t);
        $ibeta = $this->regularizedIncompleteBeta($df / 2, 0.5, $x);

        if ($t >= 0) {
            return 1 - 0.5 * $ibeta;
        }

        return 0.5 * $ibeta;
    }

    public function cdfRounded(float $t, int $precision = 3): float
    {
        return round($this->cdf($t), $precision);
    }

    /**
     * Inverse CDF (quantile function) using Newton-Raphson iteration.
     *
     * @param float $p Probability in (0, 1) exclusive.
     * @return float The t-value such that cdf(t) = p.
     * @throws InvalidDataInputException If p is not in (0, 1).
     */
    public function invCdf(float $p): float
    {
        if ($p <= 0.0 || $p >= 1.0) {
            throw new InvalidDataInputException('p must be in the range (0, 1) exclusive.');
        }

        // Use normal approximation as initial guess
        $normalDist = new NormalDist(0.0, 1.0);
        $x = $normalDist->invCdf($p);

        // Newton-Raphson iteration
        $maxIter = 100;
        $tol = 1e-12;
        for ($i = 0; $i < $maxIter; $i++) {
            $fx = $this->cdf($x) - $p;
            $fpx = $this->pdf($x);
            if ($fpx < 1e-15) {
                break;
            }
            $delta = $fx / $fpx;
            $x -= $delta;
            if (abs($delta) < $tol) {
                break;
            }
        }

        return $x;
    }

    public function invCdfRounded(float $p, int $precision = 3): float
    {
        return round($this->invCdf($p), $precision);
    }

    /**
     * Log-gamma function using the Lanczos approximation.
     */
    private static function logGamma(float $x): float
    {
        // Lanczos approximation coefficients (g=7, n=9)
        $coef = [
            0.99999999999980993,
            676.5203681218851,
            -1259.1392167224028,
            771.32342877765313,
            -176.61502916214059,
            12.507343278686905,
            -0.13857109526572012,
            9.9843695780195716e-6,
            1.5056327351493116e-7,
        ];

        if ($x < 0.5) {
            // Reflection formula: Gamma(x) * Gamma(1-x) = pi / sin(pi*x)
            return log(M_PI / sin(M_PI * $x)) - self::logGamma(1.0 - $x);
        }

        $x -= 1.0;
        $a = $coef[0];
        $t = $x + 7.5; // g + 0.5
        for ($i = 1; $i < 9; $i++) {
            $a += $coef[$i] / ($x + $i);
        }

        return 0.5 * log(2.0 * M_PI) + ($x + 0.5) * log($t) - $t + log($a);
    }

    /**
     * Regularized incomplete beta function I_x(a, b).
     *
     * Uses the continued fraction expansion (Lentz's algorithm).
     */
    private function regularizedIncompleteBeta(float $a, float $b, float $x): float
    {
        // Use the symmetry relation when x > (a+1)/(a+b+2) for better convergence
        if ($x > ($a + 1) / ($a + $b + 2)) {
            return 1.0 - $this->regularizedIncompleteBeta($b, $a, 1.0 - $x);
        }

        // Log of the front factor: x^a * (1-x)^b / (a * B(a,b))
        $logFront = $a * log($x) + $b * log(1 - $x) - log($a)
            - (self::logGamma($a) + self::logGamma($b) - self::logGamma($a + $b));
        $front = exp($logFront);

        return $front * $this->incompleteBetaCf($a, $b, $x);
    }

    /**
     * Continued fraction expansion for the incomplete beta function.
     * Uses the modified Lentz's algorithm.
     */
    private function incompleteBetaCf(float $a, float $b, float $x): float
    {
        $maxIter = 200;
        $eps = 1e-15;
        $tiny = 1e-30;

        $f = 1.0;
        $c = 1.0;
        $d = 1.0 - ($a + $b) * $x / ($a + 1);
        if (abs($d) < $tiny) {
            $d = $tiny; // @codeCoverageIgnore
        }
        $d = 1.0 / $d;
        $f = $d;

        for ($m = 1; $m <= $maxIter; $m++) {
            // Even step
            $numerator = $m * ($b - $m) * $x / (($a + 2 * $m - 1) * ($a + 2 * $m));

            $d = 1.0 + $numerator * $d;
            if (abs($d) < $tiny) {
                $d = $tiny; // @codeCoverageIgnore
            }
            $c = 1.0 + $numerator / $c;
            if (abs($c) < $tiny) {
                $c = $tiny; // @codeCoverageIgnore
            }
            $d = 1.0 / $d;
            $f *= $d * $c;

            // Odd step
            $numerator = -(($a + $m) * ($a + $b + $m) * $x) / (($a + 2 * $m) * ($a + 2 * $m + 1));

            $d = 1.0 + $numerator * $d;
            if (abs($d) < $tiny) {
                $d = $tiny; // @codeCoverageIgnore
            }
            $c = 1.0 + $numerator / $c;
            if (abs($c) < $tiny) {
                $c = $tiny; // @codeCoverageIgnore
            }
            $d = 1.0 / $d;
            $delta = $d * $c;
            $f *= $delta;

            if (abs($delta - 1.0) < $eps) {
                break;
            }
        }

        return $f;
    }
}
