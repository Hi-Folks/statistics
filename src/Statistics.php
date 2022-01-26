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
        $del_val = 0;
        $this->values = array_values(array_filter($this->values, function ($e) use ($del_val) {
            return ($e != $del_val);
        }));

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
     * @return array<double>
     */
    public function relativeFrequencies(): array
    {
        return Freq::relativeFrequencies($this->values);
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
    public function getCumulativeFrequences(): array
    {
        $freqCumul = [];
        $cumul = 0;
        foreach ($this->frequencies() as $key => $value) {
            $cumul = $cumul + $value;
            $freqCumul[$key] = $cumul;
        }

        return $freqCumul;
    }

    public function getMax(): mixed
    {
        return max($this->values);
    }

    public function getMin(): mixed
    {
        return min($this->values);
    }

    public function getRange(): mixed
    {
        return $this->getMax() - $this->getMin();
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

    public function lowerPercentile(): mixed
    {
        $count = $this->count();
        if (! $count) {
            return null;
        }
        $index = floor($count / 4);  // cache the index
        if ($count & 1) {    // count is odd
            return $this->values[$index];
        } else {                   // count is even
            return ($this->values[$index - 1] + $this->values[$index]) / 2;
        }
    }

    public function higherPercentile(): mixed
    {
        $count = $this->count();
        if (! $count) {
            return null;
        }
        $index = floor(($count * 3) / 4);  // cache the index
        if ($count & 1) {    // count is odd
            return $this->values[$index];
        } else {                   // count is even
            return ($this->values[$index - 1] + $this->values[$index]) / 2;
        }
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
     * Returns a string with values joined with a separator
     */
    public function valuesToString(bool|int $sample = false): string
    {
        if ($sample) {
            return implode(",", array_slice($this->values, 0, $sample));
        }

        return implode(",", $this->values);
    }
}
