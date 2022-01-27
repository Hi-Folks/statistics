<?php

namespace HiFolks\Statistics;

class Freq
{
    /**
     * Return true is the type of the variable is integer, boolean or string
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
     * @param mixed[] $data
     * @param bool $transformToInteger
     * @return int[]
     */
    public static function frequencies(array $data, bool $transformToInteger = false): array
    {
        if (Stat::count($data) === 0) {
            return [];
        }

        if (($transformToInteger) | (
            ! self::isDiscreteType($data[0])
        )
        ) {
            foreach ($data as $key => $value) {
                $data[$key] = intval($value);
            }
        }
        $frequencies = array_count_values($data);
        ksort($frequencies);

        return $frequencies;
    }

    /**
     * @param mixed[] $data
     * @return float[]
     */
    public static function cumulativeFrequencies(array $data): array
    {
        $freqCumul = [];
        $cumul = 0;
        $freqs = self::frequencies($data);
        foreach ($freqs as $key => $value) {
            $cumul = $cumul + $value;
            $freqCumul[$key] = $cumul;
        }

        return $freqCumul;
    }

    /**
     * @param mixed[] $data
     * @param int $round
     * @return array<double>
     */
    public static function relativeFrequencies(array $data, int $round = null): array
    {
        $returnArray = [];
        $n = Stat::count($data);
        $freq = self::frequencies($data);
        foreach ($freq as $key => $value) {
            $relValue = $value * 100 / $n;
            $returnArray[$key] = is_null($round) ? $relValue : round($relValue, $round);
        }

        return $returnArray;
    }

    /**
     * @param mixed[] $data
     * @return float[]
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
     * @return mixed
     */
    public static function lowerPercentile(array $data): mixed
    {
        $count = Stat::count($data);
        if (! $count) {
            return null;
        }
        $index = floor($count / 4);  // cache the index
        if ($count & 1) {    // count is odd
            return $data[$index];
        } else {                   // count is even
            return ($data[$index - 1] + $data[$index]) / 2;
        }
    }

    /**
     * @param mixed[] $data
     * @return mixed
     */
    public static function higherPercentile(array $data): mixed
    {
        $count = Stat::count($data);
        if (! $count) {
            return null;
        }
        $index = floor(($count * 3) / 4);  // cache the index
        if ($count & 1) {    // count is odd
            return $data[$index];
        } else {                   // count is even
            return ($data[$index - 1] + $data[$index]) / 2;
        }
    }
}
