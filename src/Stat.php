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

    /**
     * Return the single most common data point from discrete or nominal data.
     * The mode (when it exists) is the most typical value and serves as a measure of central location.
     * If there are multiple modes with the same frequency, returns the first one encountered in the data.
     * If all elements occurs once, null is returned.
     * @param mixed[] $data
     * @param bool $multimode
     * @return mixed
     */
    public static function mode(array $data, bool $multimode = false): mixed
    {
        $frequencies = Freq::frequencies($data);
        if (self::count($frequencies) === 0) {
            return null;
        }
        $sameMode = true;
        foreach ($frequencies as $key => $value) {
            if ($value > 1) {
                $sameMode = false;

                break;
            }
        }
        if ($sameMode) {
            return null;
        }
        $highestFreq = max($frequencies);
        $modes = array_keys($frequencies, $highestFreq, true);
        if ($multimode) {
            return $modes;
        }

        return $modes[0];
    }

    /**
     * Return a list of the most frequently occurring values
     * @param mixed[] $data
     * @return mixed
     */
    public static function multimode(array $data): mixed
    {
        return self::mode($data, true);
    }
}
