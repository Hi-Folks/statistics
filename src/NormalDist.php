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
    public function getMeanRounded(int $precision = 3): float
    {
        return round($this->getMean(), $precision);
    }

    // Getter for standard deviation (read-only)
    public function getSigma(): float
    {
        return $this->sigma;
    }
    public function getSigmaRounded(int $precision = 3): float
    {
        return round($this->getSigma(), $precision);
    }

    /**
     * Creates a NormalDist instance from a set of data samples.
     *
     * This static method calculates the mean (μ) and standard deviation (σ)
     * from the provided array of numeric samples and initializes a new
     * NormalDist object with these values.
     *
     * @param float[] $samples An array of numeric samples to calculate the distribution.
     *                         The array must contain at least one element.
     *
     * @return NormalDist Returns a new NormalDist object with the calculated mean and standard deviation.
     *
     * @throws InvalidDataInputException If the samples array is empty or contains non-numeric values.
     *
     */
    public static function fromSamples(array $samples): self
    {
        if ($samples === []) {
            throw new InvalidDataInputException("Samples array must not be empty.");
        }
        $mean = Stat::mean($samples);
        $std_dev = Stat::stdev($samples);
        return new self((float) $mean, $std_dev);
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


    /**
     * Adds a constant or another NormalDist instance to this distribution.
     *
     * If the argument is:
     * - A constant (float): Adjusts the mean (mu), leaving sigma unchanged.
     * - A NormalDist instance: Combines the means and variances.
     *
     * @param float|NormalDist $x2 The value or NormalDist to add.
     * @return NormalDist A new NormalDist instance with the updated parameters.
     * @throws InvalidDataInputException If the argument is not a float or NormalDist.
     */
    public function add(float|NormalDist $x2): NormalDist
    {
        if ($x2 instanceof NormalDist) {
            // Add the means and combine the variances (using the Pythagorean theorem)
            $newMu = $this->mu + $x2->mu;
            $newSigma = hypot($this->sigma, $x2->sigma);
            // sqrt(sigma1^2 + sigma2^2)
            return new NormalDist($newMu, $newSigma);
        }
        // Add a constant to the mean, sigma remains unchanged
        return new NormalDist($this->mu + $x2, $this->sigma);
    }


    /**
     * Multiplies both the mean (mu) and standard deviation (sigma) by a constant.
     *
     * This method is useful for rescaling distributions, such as when changing
     * measurement units. The standard deviation is scaled by the absolute value
     * of the constant to ensure it remains non-negative.
     *
     * @param float $constant The constant by which to scale mu and sigma.
     * @return NormalDist A new NormalDist instance with scaled mu and sigma.
     */
    public function multiply(float $constant): NormalDist
    {
        return new self(
            $this->mu * $constant,                  // Scale the mean
            $this->sigma * abs($constant),          // Scale the standard deviation by the absolute value of the constant
        );
    }



}
