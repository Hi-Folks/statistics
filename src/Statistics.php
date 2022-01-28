<?php

namespace HiFolks\Statistics;

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
     * Return the sample arithmetic mean of data
     * The arithmetic mean is the sum of the data divided by the number of data points.
     * It is commonly called “the average”,
     * although it is only one of many different mathematical averages.
     * It is a measure of the central location of the data.
     * If data is empty, null is returned
     * @return mixed
     */
    public function mean(): mixed
    {
        return Stat::mean($this->values);
    }

    /**
     * Return the median (middle value) of numeric data,
     * using the common “mean of middle two” method.
     * If data is empty, null is returned
     * @return mixed
     */
    public function median(): mixed
    {
        return Stat::median($this->values);
    }

    /**
     * @return mixed
     */
    public function lowerPercentile(): mixed
    {
        return Stat::lowerPercentile($this->values);
    }

    public function higherPercentile(): mixed
    {
        return Stat::higherPercentile($this->values);
    }

    /**
     * @return mixed
     */
    public function interquartileRange()
    {
        return $this->higherPercentile() - $this->lowerPercentile();
    }

    /**
     * The most frequent value
     */
    public function mode(): mixed
    {
        return Stat::mode($this->values);
    }

    /**
     * @param int|null $round
     * @return mixed
     */
    public function stdev(?int $round = null): mixed
    {
        return Stat::stdev($this->values, $round);
    }

    public function variance(?int $round = null): mixed
    {
        return Stat::variance($this->values, $round);
    }

    /**
     * @param int|null $round
     * @return mixed
     */
    public function pstdev(?int $round = null): mixed
    {
        return Stat::pstdev($this->values, $round);
    }

    public function pvariance(?int $round = null): mixed
    {
        return Stat::pvariance($this->values, $round);
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
