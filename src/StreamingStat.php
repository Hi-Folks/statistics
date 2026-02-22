<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Exception\InvalidDataInputException;

/**
 * StreamingStat computes descriptive statistics in a single pass with O(1) memory.
 *
 * Uses Welford's online algorithm (extended by Terriberry/Pébay) to maintain
 * running moments, ideal for large datasets or generator-based streams.
 *
 * **Experimental** in version 1.x — will be released as stable in version 2.
 */
class StreamingStat
{
    private int $n = 0;

    private float $mu = 0.0;

    private float $m2 = 0.0;

    private float $m3 = 0.0;

    private float $m4 = 0.0;

    private float $sum = 0.0;

    private float $min = PHP_FLOAT_MAX;

    private float $max = -PHP_FLOAT_MAX;

    /**
     * Add a value and update all accumulators using the online algorithm.
     */
    public function add(int|float $value): self
    {
        $this->sum += $value;
        if ($value < $this->min) {
            $this->min = (float) $value;
        }
        if ($value > $this->max) {
            $this->max = (float) $value;
        }

        $n1 = $this->n;
        $this->n++;
        $n = $this->n;

        $delta = $value - $this->mu;
        $deltaN = $delta / $n;
        $deltaN2 = $deltaN * $deltaN;
        $term1 = $delta * $deltaN * $n1;

        $this->mu += $deltaN;
        $this->m4 += $term1 * $deltaN2 * ($n * $n - 3 * $n + 3)
            + 6 * $deltaN2 * $this->m2
            - 4 * $deltaN * $this->m3;
        $this->m3 += $term1 * $deltaN * ($n - 2)
            - 3 * $deltaN * $this->m2;
        $this->m2 += $term1;

        return $this;
    }

    /**
     * Return the number of values added.
     */
    public function count(): int
    {
        return $this->n;
    }

    /**
     * Return the sum of all values added.
     */
    public function sum(): float
    {
        if ($this->n < 1) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        return $this->sum;
    }

    /**
     * Return the minimum value added.
     */
    public function min(): float
    {
        if ($this->n < 1) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        return $this->min;
    }

    /**
     * Return the maximum value added.
     */
    public function max(): float
    {
        if ($this->n < 1) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        return $this->max;
    }

    /**
     * Return the arithmetic mean.
     */
    public function mean(?int $round = null): float
    {
        if ($this->n < 1) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        return Math::round($this->mu, $round);
    }

    /**
     * Return the sample variance (m2 / (n - 1)).
     */
    public function variance(?int $round = null): float
    {
        if ($this->n < 2) {
            throw new InvalidDataInputException(
                "The data size must be greater than 1.",
            );
        }

        return Math::round($this->m2 / ($this->n - 1), $round);
    }

    /**
     * Return the population variance (m2 / n).
     */
    public function pvariance(?int $round = null): float
    {
        if ($this->n < 1) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        return Math::round($this->m2 / $this->n, $round);
    }

    /**
     * Return the sample standard deviation.
     */
    public function stdev(?int $round = null): float
    {
        return Math::round(sqrt($this->variance()), $round);
    }

    /**
     * Return the population standard deviation.
     */
    public function pstdev(?int $round = null): float
    {
        return Math::round(sqrt($this->pvariance()), $round);
    }

    /**
     * Return the adjusted Fisher-Pearson sample skewness.
     */
    public function skewness(?int $round = null): float
    {
        if ($this->n < 3) {
            throw new InvalidDataInputException("Skewness requires at least 3 data points.");
        }

        if ($this->m2 === 0.0) {
            throw new InvalidDataInputException("Skewness is undefined when all values are identical (standard deviation is zero).");
        }

        $n = $this->n;
        // population skewness: (sqrt(n) * m3) / m2^1.5
        $populationSkew = (sqrt($n) * $this->m3) / ($this->m2 ** 1.5);
        // sample adjustment: n / ((n-1)(n-2)) * n * populationSkew
        // which simplifies to: (n * sqrt(n)) / ((n-1)*(n-2)) * m3 / m2^1.5
        // Actually the adjustment from population to sample skewness:
        // G1 = (sqrt(n*(n-1)) / (n-2)) * g1
        // where g1 = populationSkew
        $skewness = (sqrt($n * ($n - 1)) / ($n - 2)) * $populationSkew;

        return Math::round($skewness, $round);
    }

    /**
     * Return the population (biased) skewness.
     */
    public function pskewness(?int $round = null): float
    {
        if ($this->n < 3) {
            throw new InvalidDataInputException("Skewness requires at least 3 data points.");
        }

        if ($this->m2 === 0.0) {
            throw new InvalidDataInputException("Skewness is undefined when all values are identical (standard deviation is zero).");
        }

        $n = $this->n;
        $pskewness = (sqrt($n) * $this->m3) / ($this->m2 ** 1.5);

        return Math::round($pskewness, $round);
    }

    /**
     * Return the excess kurtosis (sample, Fisher=True, bias=False).
     * Same formula as Excel's KURT() and scipy.stats.kurtosis(bias=False).
     */
    public function kurtosis(?int $round = null): float
    {
        if ($this->n < 4) {
            throw new InvalidDataInputException("Kurtosis requires at least 4 data points.");
        }

        if ($this->m2 === 0.0) {
            throw new InvalidDataInputException("Kurtosis is undefined when all values are identical (standard deviation is zero).");
        }

        $n = $this->n;
        // population excess kurtosis: (n * m4) / (m2^2) - 3
        $populationKurtosis = ($n * $this->m4) / ($this->m2 * $this->m2) - 3;
        // sample adjustment:
        // G2 = ((n-1) / ((n-2)(n-3))) * ((n+1) * g2 + 6)
        $kurtosis = (($n - 1) / (($n - 2) * ($n - 3))) * (($n + 1) * $populationKurtosis + 6);

        return Math::round($kurtosis, $round);
    }
}
