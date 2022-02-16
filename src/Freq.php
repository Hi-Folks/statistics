<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Math;

class Freq
{
    /**
     * Return true is the type of the variable is integer, boolean or string
     *
     * @param mixed $value
     * @return bool
     */
    private static function isDiscreteType(mixed $value): bool
    {
        $type = gettype($value);

        return in_array($type, ["string", "boolean", "integer"]);
    }

    /**
     * Return an array with the number of occurrences of each element.
     * Useful for the frequencies table.
     *
     * @param mixed[] $data
     * @param bool $transformToInteger whether data should be transformed to integer
     * @return array<mixed, int>
     */
    public static function frequencies(array $data, bool $transformToInteger = false): array
    {
        if (! Stat::count($data)) {
            return [];
        }
        if ($transformToInteger || ! self::isDiscreteType($data[0])) {
            foreach ($data as $key => $value) {
                $data[$key] = intval($value);
            }
        }
        $frequencies = array_count_values($data);
        ksort($frequencies);

        return $frequencies;
    }

    /**
     * Calculate cumulative (number of occurrences of element + sum of the numbers of occurrences of the elements,
     * that come before that element)frequency of elements.
     * For the array like ['A', 'A', 'B', 'C'] it would be ['A' => 2, 'B' => 3, 'C' => 4]
     *
     * @param mixed[] $data
     * @return array<mixed, int>
     */
    public static function cumulativeFrequencies(array $data): array
    {
        /**
         * @var array<mixed, int> array of cumulative frequencies
         */
        $freqCumul = [];
        /**
         * @var int cumulative frequency
         */
        $cumul = 0;
        $freqs = self::frequencies($data);
        foreach ($freqs as $key => $value) {
            $cumul += $value;
            $freqCumul[$key] = $cumul;
        }

        return $freqCumul;
    }

    /**
     * Calculate relative frequencies. Basically it is the percentage of occurrences of each element in the array.
     * For the array like ['A', 'A', 'B', 'C'] it would be ['A' => 50, 'B' => 25, 'C' => 25]
     *
     * @param mixed[] $data
     * @param ?int $round whether to round values or not
     * @return array<mixed, float>
     */
    public static function relativeFrequencies(array $data, ?int $round = null): array
    {
        $returnArray = [];
        $n = Stat::count($data);
        $freq = self::frequencies($data);
        foreach ($freq as $key => $value) {
            $relValue = $value * 100 / $n;
            $returnArray[$key] = Math::round($relValue, $round);
        }

        return $returnArray;
    }

    /**
     * Calculate cumulative relative frequencies.
     * For the array like ['A', 'A', 'B', 'C'] it would be ['A' => 50, 'B' => 75, 'C' => 100]
     *
     * @param mixed[] $data
     * @return array<mixed, float>
     */
    public static function cumulativeRelativeFrequencies(array $data): array
    {
        $freqCumul = [];
        $cumul = 0;
        $relFreqs = self::relativeFrequencies($data);
        foreach ($relFreqs as $key => $value) {
            $cumul = $cumul + $value;
            $freqCumul[$key] = $cumul;
        }

        return $freqCumul;
    }

    /**
     * @param mixed[] $data
     * @param int $chunkSize
     * @return int[]
     */
    public static function frequencyTableBySize(array $data, int $chunkSize = 1): array
    {
        $result = [];
        $min = floor(min($data));
        $max = ceil(max($data));
        //$limit = ceil(($max - $min) / $category);

        sort($data);
        $rangeLow = $min;
        $rangeHigh = $rangeLow;
        while ($rangeHigh < $max) {
            $count = 0;
            $rangeHigh = ($rangeLow + $chunkSize);
            foreach ($data as $key => $number) {
                if (
                    ($number >= $rangeLow)
                    &&
                    ($number < $rangeHigh)
                ) {
                    $count++;
                    //unset($data[$key]);
                }
            }
            $result[strval($rangeLow)] = $count;
            $rangeLow = $rangeHigh;
        }

        return $result;
    }

    /**
     * Returns the frequency table grouped by class.
     * The parameter $category set the number of classes.
     * If $category is null (default value for the optional parameter),
     * each class is not a range.
     * @param mixed[] $data
     * @param ?int $category
     * @return int[]
     */
    public static function frequencyTable(array $data, int $category = null): array
    {
        $result = [];
        $min = floor(min($data));
        $max = ceil(max($data));
        if (is_null($category)) {
            $category = ($max - $min) + 1;
        }

        $limit = ceil(($max - $min) / $category);
        sort($data);
        $rangeLow = $min;
        for ($i = 0; $i < $category; $i++) {
            $count = 0;
            $rangeHigh = $rangeLow + $limit;
            foreach ($data as $key => $number) {
                if (
                    ($number >= $rangeLow)
                    &&
                    ($number < $rangeHigh)
                ) {
                    $count++;
                    //unset($data[$key]);
                }
            }
            $result[strval($rangeLow)] = $count;
            $rangeLow = $rangeHigh;
        }

        // eliminate
        foreach ($result as $key => $item) {
            if ($key > max($data)) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
