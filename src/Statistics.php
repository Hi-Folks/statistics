<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\ArrUtil;
use HiFolks\Statistics\Stat;

class Statistics
{
    /**
     * Original array (no sorted and with original keys)
     * @var array<mixed>
     */
    private array $originalArray = [];

    /**
     * Sorted values, with 0 index
     * @var array<mixed>
     */
    private array $values = [];

    /**
     * @param array<mixed> $values
     */
    public function __construct(
        array $values = []
    ) {
        $this->values = array_values($values);
        $this->originalArray = $values;
        sort($this->values);
    }

    /**
     * @param array<mixed> $values
     * @return self
     */
    public static function make(array $values): self
    {
        $freqTable = new self($values);

        return $freqTable;
    }

    public function stripZeroes(): self
    {
        $this->values = ArrUtil::stripZeroes($this->values);

        return $this;
    }

    /**
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
     * @param bool $transformToInteger
     * @return array<int>
     */
    public function frequencies(bool $transformToInteger = false): array
    {
        return Freq::frequencies($this->values, $transformToInteger);
    }

    /**
     * @param int $round
     * @return array<double>
     */
    public function relativeFrequencies(int $round = null): array
    {
        return Freq::relativeFrequencies($this->values, $round);
    }

    /**
     * @return array<double>
     */
    public function cumulativeRelativeFrequencies(): array
    {
        return Freq::cumulativeRelativeFrequencies($this->values);
    }

    /**
     * @return array<double>
     */
    public function cumulativeFrequencies(): array
    {
        return Freq::cumulativeFrequencies($this->values);
    }

    public function max(): mixed
    {
        return max($this->values);
    }

    public function min(): mixed
    {
        return min($this->values);
    }

    public function range(): mixed
    {
        return $this->max() - $this->min();
    }

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
        return Stat::mean($this->values);
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
     * @param int|null $round whether to round the result
     * @see Stat::stdev()
     */
    public function stdev(?int $round = null): ?float
    {
        return Stat::stdev($this->values, $round);
    }
    /**
     * Return the variance from the numeric data
     *
     * @param int|null $round whether to round the result
     * @see Stat::variance()
     */
    public function variance(?int $round = null): ?float
    {
        return Stat::variance($this->values, $round);
    }

    /**
     * Return the **population** standard deviation.
     *
     * @param int|null whether to round the result
     * @see Stat::pstdev()
     */
    public function pstdev(?int $round = null): ?float
    {
        return Stat::pstdev($this->values, $round);
    }

    /**
     * Return dispersion of the numeric data.
     *
     * @param int|null whether to round the result
     * @see Stat::pvariance()
     */
    public function pvariance(?int $round = null): ?float
    {
        return Stat::pvariance($this->values, $round);
    }

    /**
     * Return the geometric mean of the numeric data.
     *
     * @param int|null $round whether to round the result
     * @see Stat::geometricMean()
     */
    public function geometricMean(?int $round = null): ?float
    {
        return Stat::geometricMean($this->values, $round);
    }

    /**
     * Return the harmonic mean of the numeric data.
     * @param int|null $round whether to round the result
     * @see Stat::harmonicMean()
     */
    public function harmonicMean(?int $round = null): ?float
    {
        return Stat::harmonicMean($this->values, null, $round);
    }

    /**
     * Returns a string with values joined with a separator
     * @param bool|int $sample
     * @return string
     */
    public function valuesToString(bool|int $sample = false): string
    {
        return ArrUtil::toString($this->values, $sample);
    }
}
