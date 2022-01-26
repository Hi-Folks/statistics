<?php

namespace HiFolks\Statistics;

class Freq
{
    public const MEDIAN_TYPE_LOW = "LOW";
    public const MEDIAN_TYPE_HIGH = "HIGH";
    public const MEDIAN_TYPE_MIDDLE = "MIDDLE";

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
}
