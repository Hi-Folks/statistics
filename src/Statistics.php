<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Exception\InvalidDataInputException;

class Statistics
{
    /**
     * Original array (no sorted and with original keys)
     *
     * @var array<mixed>
     */
    private array $originalArray = [];

    /**
     * Sorted values, with 0 index
     *
     * @var array<mixed>
     */
    private array $values = [];

    /**
     * Whether array contains not a numbers
     */
    private ?bool $containsNan = null;

    /**
     * @param  array<mixed>  $values
     */
    public function __construct(
        array $values = [],
    ) {
        $this->values = array_values($values);
        $this->originalArray = $values;
        sort($this->values);
    }

    /**
     * @param  array<mixed>  $values
     */
    public static function make(array $values): self
    {
        return new self($values);
    }

    /**
     * Remove '0' values from the array.
     */
    public function stripZeroes(): self
    {
        $this->values = ArrUtil::stripZeroes($this->values);

        return $this;
    }

    /**
     * Get the original array.
     *
     * @return mixed[]
     */
    public function originalArray(): array
    {
        return $this->originalArray;
    }

    /**
     * Create a frequencies table.
     * It counts the occurrences of each value in the array
     * For not discrete elements you can try to transform to integer
     *
     * @see Freq::frequencies()
     *
     * @return array<int>
     */
    public function frequencies(bool $transformToInteger = false): array
    {
        return Freq::frequencies($this->values, $transformToInteger);
    }

    /**
     * Return relative frequencies table.
     *
     * @see Freq::relativeFrequencies()
     *
     * @param  int|null  $round whether to round the result
     * @return array<float>
     */
    public function relativeFrequencies(?int $round = null): array
    {
        return Freq::relativeFrequencies($this->values, $round);
    }

    /**
     * Return cumulative relative frequencies table.
     *
     * @see Freq::cumulativeRelativeFrequencies()
     *
     * @return array<float>
     */
    public function cumulativeRelativeFrequencies(): array
    {
        return Freq::cumulativeRelativeFrequencies($this->values);
    }

    /**
     * Return cumulative frequencies table.
     *
     * @see Freq::cumulativeFrequencies()
     *
     * @return array<float>
     */
    public function cumulativeFrequencies(): array
    {
        return Freq::cumulativeFrequencies($this->values);
    }

    /**
     * Get the highest value.
     */
    public function max(): mixed
    {
        if ($this->values === []) {
            return 0;
        }
        return max($this->values);
    }

    /**
     * Get the lowest value.
     */
    public function min(): mixed
    {
        if ($this->values === []) {
            return 0;
        }
        return min($this->values);
    }

    /**
     * Get the range (max value - min value).
     */
    public function range(): int|float
    {
        return $this->max() - $this->min();
    }

    /**
     * Count elements.
     */
    public function count(): int
    {
        return Stat::count($this->values);
    }

    /**
     * Return the arithmetic mean of numeric data.
     *
     * @see Stat::mean()
     */
    public function mean(): int|float|null
    {
        return Stat::mean($this->numericalArray());
    }

    /**
     * Return the median (middle value) of data.
     *
     * @see Stat::median()
     */
    public function median(): mixed
    {
        return Stat::median($this->values);
    }

    /**
     * Estimate the median for grouped data.
     *
     * @param  float  $interval the width of each bin
     *
     * @see Stat::medianGrouped()
     */
    public function medianGrouped(float $interval = 1.0): float
    {
        return Stat::medianGrouped($this->numericalArray(), $interval);
    }

    /**
     * Return the first quartile.
     *
     * @see Stat::firstQuartile()
     */
    public function firstQuartile(): mixed
    {
        return Stat::firstQuartile($this->values);
    }

    /**
     * Return the third quartile.
     *
     * @see Stat::thirdQuartile()
     */
    public function thirdQuartile(): mixed
    {
        return Stat::thirdQuartile($this->values);
    }

    /**
     * Return the interquartile range or midspread.
     */
    public function interquartileRange(): mixed
    {
        return $this->thirdQuartile() - $this->firstQuartile();
    }

    /**
     * Return the most common data point from discrete or nominal data
     *
     * @see Stat::mode()
     */
    public function mode(): mixed
    {
        return Stat::mode($this->values);
    }

    /**
     * Return the standard deviation of the numeric data.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::stdev()
     */
    public function stdev(?int $round = null): float
    {
        return Stat::stdev($this->numericalArray(), $round);
    }

    /**
     * Return the variance from the numeric data
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::variance()
     */
    public function variance(?int $round = null): float
    {
        return Stat::variance($this->numericalArray(), $round);
    }

    /**
     * Return the **population** standard deviation.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::pstdev()
     */
    public function pstdev(?int $round = null): float
    {
        return Stat::pstdev($this->numericalArray(), $round);
    }

    /**
     * Return dispersion of the numeric data.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::pvariance()
     */
    public function pvariance(?int $round = null): float
    {
        return Stat::pvariance($this->numericalArray(), $round);
    }

    /**
     * Return the adjusted Fisher-Pearson sample skewness.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::skewness()
     */
    public function skewness(?int $round = null): float
    {
        return Stat::skewness($this->numericalArray(), $round);
    }

    /**
     * Return the population (biased) skewness.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::pskewness()
     */
    public function pskewness(?int $round = null): float
    {
        return Stat::pskewness($this->numericalArray(), $round);
    }

    /**
     * Return the excess kurtosis (sample formula).
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::kurtosis()
     */
    public function kurtosis(?int $round = null): float
    {
        return Stat::kurtosis($this->numericalArray(), $round);
    }

    /**
     * Return the geometric mean of the numeric data.
     *
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::geometricMean()
     */
    public function geometricMean(?int $round = null): float
    {
        return Stat::geometricMean($this->numericalArray(), $round);
    }

    /**
     * Return the harmonic mean of the numeric data.
     *
     * @param  int|null  $round whether to round the result
     * @param  mixed[]  $weights additional weight to the elements (as if there were several of them)
     *
     * @see Stat::harmonicMean()
     */
    public function harmonicMean(?int $round = null, ?array $weights = null): float
    {
        return Stat::harmonicMean($this->numericalArray(), $weights, $round);
    }

    /**
     * Returns a string with values joined with a separator
     */
    public function valuesToString(bool|int $sample = false): string
    {
        return ArrUtil::toString($this->values, $sample);
    }

    /**
     * Caching-check for array to be numerical (for some functions).
     *
     * @return array<int|float>
     */
    public function numericalArray(): array
    {
        if ($this->containsNan === null) {
            foreach ($this->values as $value) {
                if (!is_numeric($value)) {
                    $this->containsNan = true;

                    break;
                }
            }
        }
        if ($this->containsNan) {
            throw new InvalidDataInputException('The data must not contain non-number elements.');
        }

        return $this->values;
    }
}
