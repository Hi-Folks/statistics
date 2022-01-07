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
    public function getOriginalArray(): array
    {
        return $this->originalArray;
    }

    /**
     * @param bool $transformToInteger
     * @return array<int>
     */
    public function getFrequencies(bool $transformToInteger = false): array
    {
        if ($this->getCount() === 0) {
            return [];
        }

        if (($transformToInteger) | (
            ! is_int($this->values[0])
        )
            ) {
            foreach ($this->values as $key => $value) {
                $this->values[$key] = intval($value);
            }
        }
        $frequences = array_count_values($this->values);
        ksort($frequences);

        return $frequences;
    }

    /**
     * @return array<double>
     */
    public function getRelativeFrequencies(): array
    {
        $returnArray = [];
        $n = $this->getCount();
        foreach ($this->getFrequencies() as $key => $value) {
            $returnArray[$key] = $value * 100 / $n;
        }

        return $returnArray;
    }

    /**
     * @return array<double>
     */
    public function getCumulativeRelativeFrequencies(): array
    {
        $freqCumul = [];
        $cumul = 0;
        foreach ($this->getRelativeFrequencies() as $key => $value) {
            $cumul = $cumul + $value;
            $freqCumul[$key] = $cumul;
        }

        return $freqCumul;
    }

    /**
     * @return array<double>
     */
    public function getCumulativeFrequences(): array
    {
        $freqCumul = [];
        $cumul = 0;
        foreach ($this->getFrequencies() as $key => $value) {
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

    public function getCount(): int
    {
        return count($this->values);
    }

    public function getMean(): mixed
    {
        $sum = 0;
        if ($this->getCount() === 0) {
            return null;
        }
        foreach ($this->values as $key => $value) {
            $sum = $sum + $value;
        }

        return $sum / $this->getCount();
    }

    public function getMedian(): mixed
    {
        $count = $this->getCount();
        if (! $count) {
            return null;
        }
        $index = floor($count / 2);  // cache the index
        if ($count & 1) {    // count is odd
            return $this->values[$index];
        } else {                   // count is even
            return ($this->values[$index - 1] + $this->values[$index]) / 2;
        }
    }

    public function getLowerPercentile(): mixed
    {
        $count = $this->getCount();
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

    public function getHigherPercentile(): mixed
    {
        $count = $this->getCount();
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
    public function getInterQuartileRange()
    {
        return $this->getHigherPercentile() - $this->getLowerPercentile();
    }

    /**
     * The most frequent value
     */
    public function getMode(): mixed
    {
        $frequences = $this->getFrequencies();
        if (count($frequences) === 0) {
            return null;
        }
        $sameMode = true;
        foreach ($frequences as $key => $value) {
            if ($value > 1) {
                $sameMode = false;

                break;
            }
        }
        if ($sameMode) {
            return null;
        }
        $highestFreq = max($frequences);
        $modes = array_keys($frequences, $highestFreq, true);

        return $modes[0];
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
