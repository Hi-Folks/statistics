<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Math;
use HiFolks\Statistics\Freq;

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
     * Return the sample arithmetic mean of numeric data
     * The arithmetic mean is the sum of the data divided by the number of data points.
     * It is commonly called “the average”,
     * although it is only one of many different mathematical averages.
     * It is a measure of the central location of the data.
     * If data is empty, null is returned
     * @param array<int|float> $data array of data
     * @return int|float|null arithmetic mean or null if data is empty
     */
    public static function mean(array $data): int|float|null
    {
        $sum = 0;
        if (! self::count($data)) {
            return null;
        }
        foreach ($data as $value) {
            $sum = $sum + $value;
        }

        return $sum / self::count($data);
    }

    /**
     * Return the median (middle value) of data,
     * using the common “mean of middle two” method.
     * @param mixed[] $data
     * @param string $medianType
     * @return mixed|null median of the data or null if data is empty
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
     * Return the low median of data.
     * The low median is always a member of the data set.
     * When the number of data points is odd, the middle value is returned.
     * When it is even, the smaller of the two middle values is returned.
     * @param mixed[] $data
     * @return mixed|null low median of the data or null if data is empty
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
     * @param mixed[] $data
     * @return mixed|null high median of the data or null if data is empty
     */
    public static function medianHigh(array $data): mixed
    {
        return self::median($data, self::MEDIAN_TYPE_HIGH);
    }

    /**
     * Return the most common data point from discrete or nominal data.
     * The mode (when it exists) is the most typical value and serves as a measure of central location.
     * If there are multiple modes with the same frequency, returns the first one encountered in the data.
     * @param mixed[] $data
     * @param bool $multimode whether to return all the modes
     * @return mixed|mixed[]|null the most common data point, array of them or null if all elements occurs once
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
     * @return mixed[]|null array of the most common data points or null, if all elements occurs once
     */
    public static function multimode(array $data): mixed
    {
        return self::mode($data, true);
    }

    /**
     * Return the quantiles of the data.
     * @param mixed[] $data
     * @param int $n number of quantiles
     * @param int|null $round whether to round the result
     * @return mixed[]|null array of quntiles or null if number of quantiles is less than 1, or array size is less than 2
     */
    public static function quantiles(array $data, int $n = 4, ?int $round = null): ?array
    {
        $count = Stat::count($data);
        if ($count < 2) {
            return null;
        }
        if ($n < 1) {
            return null;
        }
        sort($data);
        $m = $count + 1;
        $result = [];
        foreach (range(1, $n - 1) as $i) {
            $j = floor($i * $m / $n);
            if ($j < 1) {
                $j = 1;
            } elseif ($j > $count - 1) {
                $j = $count - 1;
            }
            $delta = $i * $m - $j * $n;
            $interpolated = ($data[$j - 1] * ($n - $delta) + $data[$j] * $delta) / $n;
            $result[] = Math::round($interpolated, $round);
        }

        return $result;
    }

    /**
     * Return the first or lower quartile a.k.a. 25th percentile.
     * @param mixed[] $data
     * @return mixed|null the first quartile or null, if it cannot be calculated (@see Stat::quantiles())
     */
    public static function firstQuartile(array $data, ?int $round = null): mixed
    {
        $quartiles = self::quantiles($data, 4, $round);
        if (is_null($quartiles)) {
            return null;
        }

        return $quartiles[0];
    }

    /**
     * Return the third or upper quartile a.k.a. 75th percentile.
     * @param mixed[] $data
     * @return mixed|null the third quartile or null, if it cannot be calculated (@see Stat::quantiles())
     */
    public static function thirdQuartile(array $data): mixed
    {
        $quartiles = self::quantiles($data, 4);
        if (is_null($quartiles)) {
            return null;
        }

        return $quartiles[2];
    }

    /**
     * Return the **Population** standard deviation,
     * a measure of the amount of variation or dispersion of a set of values.
     * A low standard deviation indicates that
     * the values tend to be close to the mean of the set,
     * while a high standard deviation indicates that
     * the values are spread out over a wider range.
     * @param mixed[] $data
     * @return float|null
     */
    public static function pstdev(array $data, int $round = null): ?float
    {
        $variance = self::pvariance($data);
        if (is_null($variance)) {
            return null;
        }

        return (float)Math::round(sqrt($variance), $round);
    }

    /**
     * @param mixed[] $data
     * @return float|null
     */
    public static function pvariance(array $data, ?int $round = null): ?float
    {
        $num_of_elements = self::count($data);
        if ($num_of_elements == 0) {
            return null;
        }
        $sumSquareDifferences = 0.0;
        $average = self::mean($data);

        foreach ($data as $i) {
            // sum of squares of differences between
            // all numbers and means.
            $sumSquareDifferences += pow(($i - $average), 2);
        }

        return Math::round($sumSquareDifferences / ($num_of_elements), $round);
    }

    /**
     * @param mixed[] $data
     * @param int|null $round
     * @return float|null
     */
    public static function stdev(array $data, int $round = null): ?float
    {
        $variance = self::variance($data);
        if (is_null($variance)) {
            return null;
        }

        return (float)Math::round(sqrt(self::variance($data)), $round);
    }

    /**
     * @param mixed[] $data
     * @return float|null
     */
    public static function variance(array $data, ?int $round = null): ?float
    {
        $num_of_elements = self::count($data);
        if ($num_of_elements <= 1) {
            return null;
        }
        $sumSquareDifferences = 0.0;
        $average = self::mean($data);

        foreach ($data as $i) {
            // sum of squares of differences between
            // all numbers and means.
            $sumSquareDifferences += pow(($i - $average), 2);
        }

        return Math::round($sumSquareDifferences / ($num_of_elements - 1), $round);
    }

    /**
     * @param mixed[] $data
     * @param int|null $round
     * @return float|null
     */
    public static function geometricMean(array $data, ?int $round = null): ?float
    {
        $count = self::count($data);
        if ($count === 0) {
            return null;
        }
        $product = 1;
        foreach ($data as $key => $value) {
            $product = $product * $value;
        }
        $geometricMean = pow($product, 1 / $count);

        return Math::round($geometricMean, $round);
    }

    /**
     * @param mixed[] $data
     * @param mixed[] $weights
     * @param int|null $round
     * @return float|null
     */
    public static function harmonicMean(array $data, ?array $weights = null, ?int $round = null): ?float
    {
        $sum = 0;
        $count = self::count($data);
        if ($count === 0) {
            return null;
        }
        $sumWeigth = 0;
        foreach ($data as $key => $value) {
            if ($value == 0) {
                return 0;
            }
            $weight = is_null($weights) ? 1 : $weights[$key];
            $sumWeigth = $sumWeigth + $weight;
            $sum = $sum + ($weight / $value);
        }

        return Math::round($sumWeigth / $sum, $round);
    }
}
