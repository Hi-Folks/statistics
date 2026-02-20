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

    // Getter for median (equals mu for a normal distribution, read-only)
    public function getMedian(): float
    {
        return $this->mu;
    }
    public function getMedianRounded(int $precision = 3): float
    {
        return round($this->getMedian(), $precision);
    }

    // Getter for mode (equals mu for a normal distribution, read-only)
    public function getMode(): float
    {
        return $this->mu;
    }
    public function getModeRounded(int $precision = 3): float
    {
        return round($this->getMode(), $precision);
    }

    // Getter for variance (sigma squared, read-only)
    public function getVariance(): float
    {
        return $this->sigma ** 2;
    }
    public function getVarianceRounded(int $precision = 3): float
    {
        return round($this->getVariance(), $precision);
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

    /**
     * Computes the standard score (z-score) describing x in terms of
     * the number of standard deviations above or below the mean.
     *
     * @param float $x The value to compute the z-score for.
     * @return float The z-score: (x - mu) / sigma.
     * @throws InvalidDataInputException If sigma is zero.
     */
    public function zscore(float $x): float
    {
        if ($this->sigma === 0.0) {
            throw new InvalidDataInputException('zscore() not defined when sigma is zero.');
        }

        return ($x - $this->mu) / $this->sigma;
    }

    public function zscoreRounded(float $x, int $precision = 3): float
    {
        return round($this->zscore($x), $precision);
    }

    /**
     * Generates n random samples from the normal distribution.
     *
     * Uses the Box-Muller transform to generate normally distributed values.
     * An optional seed can be provided for reproducible results.
     *
     * @param int $n The number of samples to generate (must be >= 1).
     * @param int|null $seed Optional seed for the random number generator.
     * @return float[] An array of n random samples.
     * @throws InvalidDataInputException If n is less than 1.
     */
    public function samples(int $n, ?int $seed = null): array
    {
        if ($n < 1) {
            throw new InvalidDataInputException('n must be at least 1.');
        }

        if ($seed !== null) {
            mt_srand($seed);
        }

        $result = [];
        for ($i = 0; $i < $n; $i++) {
            // Box-Muller transform
            $u1 = mt_rand(1, mt_getrandmax()) / mt_getrandmax();
            $u2 = mt_rand(1, mt_getrandmax()) / mt_getrandmax();
            $z = sqrt(-2.0 * log($u1)) * cos(2.0 * M_PI * $u2);
            $result[] = $this->mu + $z * $this->sigma;
        }

        return $result;
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

    // Approximate the complementary error function (erfc)
    private function erfc(float $z): float
    {
        return 1.0 - $this->erf($z);
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
     * Computes the inverse cumulative distribution function (quantile function).
     *
     * Given a probability p, finds the value x such that P(X <= x) = p.
     *
     * Uses the rational approximation algorithm by Peter Acklam
     * for the standard normal inverse CDF, then scales to (mu, sigma).
     *
     * @param float $p A probability value in the range (0, 1) exclusive.
     * @return float The value x where cdf(x) = p.
     * @throws InvalidDataInputException If p is not in (0, 1).
     */
    public function invCdf(float $p): float
    {
        if ($p <= 0.0 || $p >= 1.0) {
            throw new InvalidDataInputException('p must be in the range (0, 1) exclusive.');
        }

        // Rational approximation for the standard normal inverse CDF
        // Coefficients from Peter Acklam's algorithm
        $a = [
            -3.969683028665376e+01,
            2.209460984245205e+02,
            -2.759285104469687e+02,
            1.383577518672690e+02,
            -3.066479806614716e+01,
            2.506628277459239e+00,
        ];

        $b = [
            -5.447609879822406e+01,
            1.615858368580409e+02,
            -1.556989798598866e+02,
            6.680131188771972e+01,
            -1.328068155288572e+01,
        ];

        $c = [
            -7.784894002430293e-03,
            -3.223964580411365e-01,
            -2.400758277161838e+00,
            -2.549732539343734e+00,
            4.374664141464968e+00,
            2.938163982698783e+00,
        ];

        $d = [
            7.784695709041462e-03,
            3.224671290700398e-01,
            2.445134137142996e+00,
            3.754408661907416e+00,
        ];

        $pLow = 0.02425;
        $pHigh = 1.0 - $pLow;

        if ($p < $pLow) {
            // Rational approximation for lower region
            $q = sqrt(-2.0 * log($p));
            $x = ((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5])
                / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1.0);
        } elseif ($p <= $pHigh) {
            // Rational approximation for central region
            $q = $p - 0.5;
            $r = $q * $q;
            $x = ((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $q
                / ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + 1.0);
        } else {
            // Rational approximation for upper region
            $q = sqrt(-2.0 * log(1.0 - $p));
            $x = -((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5])
                / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1.0);
        }

        // Scale from standard normal to (mu, sigma)
        return $this->mu + $x * $this->sigma;
    }

    public function invCdfRounded(float $p, int $precision = 3): float
    {
        return round($this->invCdf($p), $precision);
    }


    /**
     * Divides the normal distribution into n continuous intervals
     * with equal probability.
     *
     * Returns an array of (n - 1) cut points separating the intervals.
     * Set n to 4 for quartiles (the default), n to 10 for deciles,
     * or n to 100 for percentiles.
     *
     * @param int $n The number of equal-probability intervals (must be >= 1).
     * @return float[] An array of (n - 1) cut points.
     * @throws InvalidDataInputException If n is less than 1.
     */
    public function quantiles(int $n = 4): array
    {
        if ($n < 1) {
            throw new InvalidDataInputException('n must be at least 1.');
        }

        $points = [];
        for ($i = 1; $i < $n; $i++) {
            $points[] = $this->invCdf($i / $n);
        }

        return $points;
    }

    /**
     * Computes the overlapping coefficient (OVL) between two normal distributions.
     *
     * Measures the agreement between two normal probability distributions.
     * Returns a value between 0.0 and 1.0 giving the overlapping area in
     * the two underlying probability density functions.
     *
     * @param NormalDist $other The other normal distribution to compare with.
     * @return float The overlapping coefficient between 0.0 and 1.0.
     * @throws InvalidDataInputException If either distribution has sigma equal to zero.
     */
    public function overlap(NormalDist $other): float
    {
        $x = $this;
        $y = $other;

        // Order so that X has the smaller (sigma, mu)
        if (($y->sigma <=> $x->sigma) ?: ($y->mu <=> $x->mu) < 0) {
            [$x, $y] = [$y, $x];
        }

        $xVar = $x->sigma ** 2;
        $yVar = $y->sigma ** 2;

        if ($xVar === 0.0 || $yVar === 0.0) {
            throw new InvalidDataInputException('overlap() not defined when sigma is zero.');
        }

        $dv = $yVar - $xVar;
        $dm = abs($y->mu - $x->mu);

        // Equal variances: simplified formula using erfc
        if ($dv === 0.0) {
            return $this->erfc($dm / (2.0 * $x->sigma * M_SQRT2));
        }

        // Unequal variances: find intersection points of the two PDFs
        $a = $x->mu * $yVar - $y->mu * $xVar;
        $b = $x->sigma * $y->sigma * sqrt($dm * $dm + $dv * log($yVar / $xVar));
        $x1 = ($a + $b) / $dv;
        $x2 = ($a - $b) / $dv;

        return 1.0 - (abs($y->cdf($x1) - $x->cdf($x1)) + abs($y->cdf($x2) - $x->cdf($x2)));
    }

    public function overlapRounded(NormalDist $other, int $precision = 3): float
    {
        return round($this->overlap($other), $precision);
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
     * Subtracts a constant or another NormalDist instance from this distribution.
     *
     * If the argument is:
     * - A constant (float): Shifts the mean (mu) down, leaving sigma unchanged.
     * - A NormalDist instance: Subtracts the means and combines the variances.
     *
     * @param float|NormalDist $x2 The value or NormalDist to subtract.
     * @return NormalDist A new NormalDist instance with the updated parameters.
     */
    public function subtract(float|NormalDist $x2): NormalDist
    {
        if ($x2 instanceof NormalDist) {
            $newMu = $this->mu - $x2->mu;
            $newSigma = hypot($this->sigma, $x2->sigma);

            return new NormalDist($newMu, $newSigma);
        }

        return new NormalDist($this->mu - $x2, $this->sigma);
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



    /**
     * Divides both the mean (mu) and standard deviation (sigma) by a constant.
     *
     * This method is useful for rescaling distributions, such as when changing
     * measurement units. The standard deviation is scaled by the absolute value
     * of the constant to ensure it remains non-negative.
     *
     * @param float $constant The constant by which to divide mu and sigma (must not be zero).
     * @return NormalDist A new NormalDist instance with scaled mu and sigma.
     * @throws InvalidDataInputException If the constant is zero.
     */
    public function divide(float $constant): NormalDist
    {
        if ($constant === 0.0) {
            throw new InvalidDataInputException('Cannot divide by zero.');
        }

        return new self(
            $this->mu / $constant,
            $this->sigma / abs($constant),
        );
    }
}
