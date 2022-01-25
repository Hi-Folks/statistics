<?php

namespace HiFolks\Statistics;

class Stat
{
    public const MEDIAN_TYPE_LOW = "LOW";
    public const MEDIAN_TYPE_HIGH = "HIGH";
    public const MEDIAN_TYPE_MIDDLE = "MIDDLE";

    /**
     * Count the element in the array
     * @param mixed[] $data
     * @return int
     */
    public static function count(array $data): int
    {
        return count($data);
    }

    /**
     * Return the sample arithmetic mean of data
     * The arithmetic mean is the sum of the data divided by the number of data points.
     * It is commonly called “the average”,
     * although it is only one of many different mathematical averages.
     * It is a measure of the central location of the data.
     * If data is empty, null is returned
     * @param mixed[] $data
     * @return mixed
     */
    public static function mean(array $data): mixed
    {
        $sum = 0;
        if (self::count($data) === 0) {
            return null;
        }
        foreach ($data as $key => $value) {
            $sum = $sum + $value;
        }

        return $sum / self::count($data);
    }

    /**
     * Return the median (middle value) of numeric data,
     * using the common “mean of middle two” method.
     * If data is empty, null is returned
     * @param mixed[] $data
     * @param string $medianType
     * @return mixed
     */
    public static function median(array $data, string $medianType = self::MEDIAN_TYPE_MIDDLE): mixed
    {
        $count = self::count($data);
        if (! $count) {
            return null;
        }
        $index = floor($count / 2);  // cache the index
        if ($count & 1) {    // count is odd
            return $data[$index];
        } else {                   // count is even
            return match ($medianType) {
                self::MEDIAN_TYPE_LOW => ($data[$index - 1]),
                self::MEDIAN_TYPE_HIGH => $data[$index],
                default => ($data[$index - 1] + $data[$index]) / 2
            };
        }
    }

    /**
     * Return the low median of numeric data.
     * The low median is always a member of the data set.
     * When the number of data points is odd, the middle value is returned.
     * When it is even, the smaller of the two middle values is returned.
     * If data is empty, null is returned
     * @param mixed[] $data
     * @return mixed
     */
    public static function medianLow(array $data): mixed
    {
        return self::median($data, self::MEDIAN_TYPE_LOW);
    }

    /**
     * Return the high median of data.
     * The high median is always a member of the data set.
     * When the number of data points is odd, the middle value is returned.
     * When it is even, the larger of the two middle values is returned.
     * If data is empty, null is returned
     * @param mixed[] $data
     * @return mixed
     */
    public static function medianHigh(array $data): mixed
    {
        return self::median($data, self::MEDIAN_TYPE_HIGH);
    }
}
