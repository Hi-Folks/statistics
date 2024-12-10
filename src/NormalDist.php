<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Exception\InvalidDataInputException;

class NormalDist
{
    // Mean

    private readonly float $sigma; // Standard deviation

    // Constructor to initialize mu and sigma
    public function __construct(private readonly float $mu = 0.0, float $sigma = 1.0)
    {
        if ($sigma < 0) {
            throw new InvalidDataInputException('Standard deviation (sigma) cannot be negative.');
        }
        $this->sigma = $sigma;
    }

    // Getter for mean (read-only)
    public function getMean(): float
    {
        return $this->mu;
    }

    // Getter for standard deviation (read-only)
    public function getSigma(): float
    {
        return $this->sigma;
    }

    // A utility function to calculate the probability density function (PDF)
    public function pdf(float $x): float
    {
        $coeff = 1 / (sqrt(2 * M_PI) * $this->sigma);
        $exponent = -($x - $this->mu) ** 2 / (2 * $this->sigma ** 2);

        return $coeff * exp($exponent);
    }

    public function pdfRounded(float $x, int $precision = 3): float
    {
        return round($this->pdf($x), $precision);
    }

    // Approximate the error function (erf)
    private function erf(float $z): float
    {
        $t = 1 / (1 + 0.5 * abs($z));
        $tau = $t * exp(-$z * $z
                - 1.26551223
                + 1.00002368 * $t
                + 0.37409196 * $t ** 2
                + 0.09678418 * $t ** 3
                - 0.18628806 * $t ** 4
                + 0.27886807 * $t ** 5
                - 1.13520398 * $t ** 6
                + 1.48851587 * $t ** 7
                - 0.82215223 * $t ** 8
                + 0.17087277 * $t ** 9);
        return $z >= 0 ? 1 - $tau : $tau - 1;
    }


    // A utility function to calculate the cumulative density function (CDF)
    public function cdf(float $x): float
    {
        $z = ($x - $this->mu) / ($this->sigma * sqrt(2));

        return 0.5 * (1 + $this->erf($z));
    }

    public function cdfRounded(float $x, int $precision = 3): float
    {
        return round($this->cdf($x), $precision);
    }



}
