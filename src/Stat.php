<?php

namespace HiFolks\Statistics;

use HiFolks\Statistics\Enums\Alternative;
use HiFolks\Statistics\Enums\KdeKernel;
use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\NormalDist;

class Stat
{
    final public const MEDIAN_TYPE_LOW = "LOW";

    final public const MEDIAN_TYPE_HIGH = "HIGH";

    final public const MEDIAN_TYPE_MIDDLE = "MIDDLE";

    /**
     * Count the element in the array
     *
     * @param  mixed[]  $data
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
     *
     * @param  array<int|float>  $data array of data
     * @return int|float|null arithmetic mean
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function mean(array $data): int|float|null
    {
        $sum = 0;
        if (self::count($data) === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        if (array_filter($data, is_string(...)) !== []) {
            throw new InvalidDataInputException(
                "The data array contains a string.",
            );
        }
        $sum = array_sum($data);

        return $sum / self::count($data);
    }

    /**
     * Calculate the float number arithmetic mean of a float numbers dataset with optional weights and precision.
     *
     * Supports both unweighted and weighted means. Automatically casts values to float.
     * Returns `null` if the input data is empty.
     *
     * @param  array<float>  $data Array of floating numbers
     * @param  null|array<float>  $weights Optional array of weights (same length as $data).
     * @param null|int $precision Optional number of decimal places to round the result (default is null, no round() is applied).
     * @return float|null arithmetic mean
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function fmean(
        array $data,
        ?array $weights = null,
        ?int $precision = null,
    ): ?float {
        $sum = 0;
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        // Unweighted mean
        if ($weights === null) {
            $sum = array_sum(array_map(floatval(...), $data));
            $count = count($data);
            if ($precision) {
                return round($sum / $count, $precision);
            }
            return $sum / $count;
        }

        // Check lengths
        if ($count !== count($weights)) {
            throw new InvalidDataInputException(
                "The data and weights must be the same length",
            );
        }

        $weightedSum = 0.0;
        $weightTotal = 0.0;
        foreach ($data as $i => $value) {
            $w = floatval($weights[$i]);
            $weightedSum += floatval($value) * $w;
            $weightTotal += $w;
        }

        if ($weightTotal == 0) {
            throw new InvalidDataInputException(
                "The sum of weights must be non-zero",
            );
        }

        if ($precision) {
            return round($weightedSum / $weightTotal, $precision);
        }
        return $weightedSum / $weightTotal;
    }

    /**
     * Return the trimmed (truncated) mean of the data.
     * Computes the mean after removing the lowest and highest fraction of values.
     * This is a robust measure of central tendency, less sensitive to outliers.
     *
     * @param  array<int|float>  $data
     * @param  float  $proportionToCut  fraction (0..0.5) to trim from each side
     * @param  int|null  $round whether to round the result
     * @return float the trimmed mean
     *
     * @throws InvalidDataInputException if the data is empty, or proportionToCut is out of range,
     *         or trimming would remove all elements
     */
    public static function trimmedMean(
        array $data,
        float $proportionToCut = 0.1,
        ?int $round = null,
    ): float {
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        if ($proportionToCut < 0 || $proportionToCut >= 0.5) {
            throw new InvalidDataInputException(
                "proportionToCut must be in the range [0, 0.5).",
            );
        }

        sort($data);
        $trimCount = (int) floor($count * $proportionToCut);
        $trimmedData = array_slice($data, $trimCount, $count - 2 * $trimCount);

        if ($trimmedData === []) {
            throw new InvalidDataInputException(
                "Trimming removed all elements.",
            );
        }

        return Math::round(
            array_sum($trimmedData) / count($trimmedData),
            $round,
        );
    }

    /**
     * Return the median (middle value) of data,
     * using the common “mean of middle two” method.
     *
     * @param  mixed[]  $data
     * @return mixed median of the data
     * @throws InvalidDataInputException if the data is empty
     */
    public static function median(
        array $data,
        string $medianType = self::MEDIAN_TYPE_MIDDLE,
    ): mixed {
        sort($data);
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        $index = (int) floor($count / 2); // cache the index
        if (($count & 1) !== 0) {
            // count is odd
            return $data[$index];
        }

        // count is even
        return match ($medianType) {
            self::MEDIAN_TYPE_LOW => $data[$index - 1],
            self::MEDIAN_TYPE_HIGH => $data[$index],
            default => ($data[$index - 1] + $data[$index]) / 2,
        };
    }

    /**
     * Return the weighted median of the data.
     * The weighted median is the value where the cumulative weight
     * reaches 50% of the total weight.
     *
     * @param  array<int|float>  $data
     * @param  array<int|float>  $weights  array of weights (same length as $data, all > 0)
     * @param  int|null  $round whether to round the result
     * @return float the weighted median
     *
     * @throws InvalidDataInputException if the data is empty, weights length mismatches,
     *         or any weight is not positive
     */
    public static function weightedMedian(array $data, array $weights, ?int $round = null): float
    {
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        if ($count !== count($weights)) {
            throw new InvalidDataInputException(
                "Data and weights must have the same number of elements.",
            );
        }

        // Validate weights and pair with data
        $paired = [];
        for ($i = 0; $i < $count; $i++) {
            if (!is_numeric($weights[$i]) || $weights[$i] <= 0) {
                throw new InvalidDataInputException(
                    "All weights must be positive numbers.",
                );
            }
            $paired[] = [(float) $data[$i], (float) $weights[$i]];
        }

        // Sort by value
        usort($paired, fn(array $a, array $b): int => $a[0] <=> $b[0]);

        $totalWeight = array_sum($weights);
        $halfWeight = $totalWeight / 2.0;
        $cumulative = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $cumulative += $paired[$i][1];
            if ($cumulative > $halfWeight) {
                return Math::round($paired[$i][0], $round);
            }
            if ($cumulative === $halfWeight && $i + 1 < $count) {
                // Exactly at the midpoint — average with the next value
                return Math::round(($paired[$i][0] + $paired[$i + 1][0]) / 2.0, $round);
            }
        }

        // Fallback: last element (all weight in one point)
        /** @var array{float, float} $last */
        $last = end($paired);

        return Math::round($last[0], $round);
    }

    /**
     * Estimate the median for grouped data that has been binned
     * around the midpoints of consecutive, fixed-width intervals.
     *
     * Uses interpolation within the median interval:
     * L + interval * (n/2 - cf) / f
     *
     * where:
     * - L is the lower limit of the median interval
     * - cf is the cumulative frequency of the preceding interval
     * - f is the number of elements in the median interval
     *
     * @param  array<int|float>  $data
     * @param  float  $interval the width of each bin
     * @return float the estimated median for grouped data
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function medianGrouped(
        array $data,
        float $interval = 1.0,
    ): float {
        sort($data);
        $n = count($data);
        if ($n === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        // Find the value at the midpoint (midpoint of the class interval)
        $x = (float) $data[intdiv($n, 2)];

        // Find where all the x values occur in the sorted data
        // All x will lie within data[i:j]
        $i = self::bisectLeft($data, $x);
        $j = self::bisectRight($data, $x, $i);

        // Lower limit of the median interval
        $L = $x - $interval / 2.0;
        // Cumulative frequency of the preceding interval
        $cf = $i;
        // Number of elements in the median interval
        $f = $j - $i;

        return $L + ($interval * ($n / 2.0 - $cf)) / $f;
    }

    /**
     * Binary search: find the leftmost position where $target can be inserted
     * in $data while keeping it sorted.
     *
     * @param  array<int|float>  $data sorted array
     * @param  float  $target value to locate
     */
    private static function bisectLeft(array $data, float $target): int
    {
        $lo = 0;
        $hi = count($data);
        while ($lo < $hi) {
            $mid = intdiv($lo + $hi, 2);
            if ($data[$mid] < $target) {
                $lo = $mid + 1;
            } else {
                $hi = $mid;
            }
        }

        return $lo;
    }

    /**
     * Binary search: find the rightmost position where $target can be inserted
     * in $data while keeping it sorted.
     *
     * @param  array<int|float>  $data sorted array
     * @param  float  $target value to locate
     * @param  int  $lo lower bound for the search
     */
    private static function bisectRight(
        array $data,
        float $target,
        int $lo = 0,
    ): int {
        $hi = count($data);
        while ($lo < $hi) {
            $mid = intdiv($lo + $hi, 2);
            if ($data[$mid] <= $target) {
                $lo = $mid + 1;
            } else {
                $hi = $mid;
            }
        }

        return $lo;
    }

    /**
     * Return the low median of data.
     * The low median is always a member of the data set.
     * When the number of data points is odd, the middle value is returned.
     * When it is even, the smaller of the two middle values is returned.
     *
     * @param  mixed[]  $data
     *
     * @see Stat::median()
     *
     * @return mixed low median of the data
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
     *
     * @param  mixed[]  $data
     *
     * @see Stat::median()
     *
     * @return mixed high median of the data
     */
    public static function medianHigh(array $data): mixed
    {
        return self::median($data, self::MEDIAN_TYPE_HIGH);
    }

    /**
     * Return the most common data point from discrete or nominal data.
     * The mode (when it exists) is the most typical value and serves as a measure of central location.
     * If there are multiple modes with the same frequency, returns the first one encountered in the data.
     *
     * @param  mixed[]  $data
     * @param  bool  $multimode whether to return all the modes
     * @return mixed|mixed[]|null the most common data point, array of them or null, if there is no mode
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function mode(array $data, bool $multimode = false): mixed
    {
        $frequencies = Freq::frequencies($data);
        if ($frequencies === []) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        $sameMode = true;
        foreach ($frequencies as $value) {
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
     *
     * @param  mixed[]  $data
     *
     * @see Stat::mode()
     *
     * @return mixed[]|null array of the most common data points or null, if all elements occurs once
     */
    public static function multimode(array $data): ?array
    {
        return self::mode($data, true);
    }

    /**
     * Return the quantiles of the data.
     *
     * @param  mixed[]  $data
     * @param  int  $n number of quantiles
     * @param  int|null  $round whether to round the result
     * @param  string  $method 'exclusive' (default) or 'inclusive'
     * @return mixed[] array of quantiles
     *
     * @throws InvalidDataInputException if number of quantiles is less than 1, or the data size is less than 2, or the method is invalid
     */
    public static function quantiles(
        array $data,
        int $n = 4,
        ?int $round = null,
        string $method = "exclusive",
    ): array {
        $count = self::count($data);
        if ($count < 2 || $n < 1) {
            throw new InvalidDataInputException(
                "The size of the data must be greater than 2 and the number of quantiles must be greater than 1.",
            );
        }

        if ($method !== "exclusive" && $method !== "inclusive") {
            throw new InvalidDataInputException(
                "Invalid method '{$method}'. Must be 'exclusive' or 'inclusive'.",
            );
        }

        sort($data);
        $result = [];

        if ($method === "inclusive") {
            $m = $count - 1;
            foreach (range(1, $n - 1) as $i) {
                $j = intdiv($i * $m, $n);
                $delta = $i * $m - $j * $n;
                $interpolated
                    = ($data[$j] * ($n - $delta) + $data[$j + 1] * $delta) / $n;
                $result[] = Math::round($interpolated, $round);
            }
        } else {
            $m = $count + 1;
            foreach (range(1, $n - 1) as $i) {
                $j = (int) floor(($i * $m) / $n);
                if ($j < 1) {
                    $j = 1;
                } elseif ($j > $count - 1) {
                    $j = $count - 1;
                }
                $delta = $i * $m - $j * $n;
                $interpolated
                    = ($data[$j - 1] * ($n - $delta) + $data[$j] * $delta) / $n;
                $result[] = Math::round($interpolated, $round);
            }
        }

        return $result;
    }

    /**
     * Return the first or lower quartile a.k.a. 25th percentile.
     *
     * @param  mixed[]  $data
     *
     * @see Stat::quantiles()
     *
     * @return mixed the first quartile
     */
    public static function firstQuartile(array $data, ?int $round = null): mixed
    {
        $quartiles = self::quantiles($data, 4, $round);

        return $quartiles[0];
    }

    /**
     * Return the third or upper quartile a.k.a. 75th percentile.
     *
     * @param  mixed[]  $data
     *
     * @see Stat::quantiles()
     *
     * @return mixed the third quartile
     */
    public static function thirdQuartile(array $data): mixed
    {
        $quartiles = self::quantiles($data, 4);

        return $quartiles[2];
    }

    /**
     * Return the value at the given percentile of the data.
     *
     * Uses linear interpolation between adjacent data points,
     * consistent with the exclusive quantile method.
     *
     * @param  array<int|float>  $data
     * @param  float  $p  percentile in range 0..100
     * @param  int|null  $round whether to round the result
     * @return float the interpolated value at the given percentile
     *
     * @throws InvalidDataInputException if the data has fewer than 2 elements or p is out of range
     */
    public static function percentile(
        array $data,
        float $p,
        ?int $round = null,
    ): float {
        $count = self::count($data);
        if ($count < 2) {
            throw new InvalidDataInputException(
                "Percentile requires at least 2 data points.",
            );
        }
        if ($p < 0 || $p > 100) {
            throw new InvalidDataInputException(
                "Percentile must be between 0 and 100.",
            );
        }

        sort($data);

        // Exclusive method: rank = p/100 * (n + 1), 1-based index
        $rank = ($p / 100) * ($count + 1);

        if ($rank <= 1) {
            return Math::round((float) $data[0], $round);
        }
        if ($rank >= $count) {
            return Math::round((float) $data[$count - 1], $round);
        }

        $lower = (int) floor($rank) - 1;
        $fraction = $rank - floor($rank);
        $interpolated
            = $data[$lower] + $fraction * ($data[$lower + 1] - $data[$lower]);

        return Math::round($interpolated, $round);
    }

    /**
     * Return the **population** standard deviation,
     * a measure of the amount of variation or dispersion of a set of values.
     * A low standard deviation indicates that
     * the values tend to be close to the mean of the set,
     * while a high standard deviation indicates that
     * the values are spread out over a wider range.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::pvariance()
     *
     * @return float the population standard deviation
     */
    public static function pstdev(array $data, ?int $round = null): float
    {
        $variance = self::pvariance($data);

        return Math::round(sqrt($variance), $round);
    }

    /**
     * Return dispersion of the numeric data.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float the dispersion of the data
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function pvariance(
        array $data,
        ?int $round = null,
        int|float|null $mu = null,
    ): float {
        $num_of_elements = self::count($data);
        if ($num_of_elements === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        $sumSquareDifferences = 0.0;
        $average = $mu ?? self::mean($data);

        foreach ($data as $i) {
            // sum of squares of differences between
            // all numbers and means.
            $sumSquareDifferences += ($i - $average) ** 2;
        }

        return Math::round($sumSquareDifferences / $num_of_elements, $round);
    }

    /**
     * Return the standard deviation of the numeric data.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     *
     * @see Stat::variance()
     *
     * @return float the standard deviation of the numeric data
     */
    public static function stdev(array $data, ?int $round = null): float
    {
        $variance = self::variance($data);

        return Math::round(sqrt($variance), $round);
    }

    /**
     * Return the standard error of the mean (SEM).
     * SEM measures how precisely the sample mean estimates the population mean.
     *
     * Formula: stdev / sqrt(n)
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float the standard error of the mean
     *
     * @throws InvalidDataInputException if data size is less than 2
     */
    public static function sem(array $data, ?int $round = null): float
    {
        return Math::round(self::stdev($data) / sqrt(self::count($data)), $round);
    }

    /**
     * Return the mean absolute deviation (MAD) of the data.
     * MAD is the average of the absolute deviations from the mean.
     *
     * Formula: (1/n) * Σ|xi - mean|
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float the mean absolute deviation
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function meanAbsoluteDeviation(array $data, ?int $round = null): float
    {
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        $mean = self::mean($data);
        $sum = 0.0;
        foreach ($data as $value) {
            $sum += abs($value - $mean);
        }

        return Math::round($sum / $count, $round);
    }

    /**
     * Return the median absolute deviation of the data.
     * This is the median of the absolute deviations from the median.
     * It is a robust measure of dispersion, highly resistant to outliers.
     *
     * Formula: median(|xi - median(x)|)
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float the median absolute deviation
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function medianAbsoluteDeviation(array $data, ?int $round = null): float
    {
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }

        $median = self::median($data);
        $deviations = [];
        foreach ($data as $value) {
            $deviations[] = abs($value - $median);
        }

        return Math::round((float) self::median($deviations), $round);
    }

    /**
     * Return the z-scores for each value in the dataset.
     * A z-score indicates how many standard deviations a value is from the mean.
     *
     * Formula: zi = (xi - mean) / stdev
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round each z-score
     * @return array<float> the z-scores
     *
     * @throws InvalidDataInputException if data size is less than 2 or stdev is zero
     */
    public static function zscores(array $data, ?int $round = null): array
    {
        $mean = self::mean($data);
        $stdev = self::stdev($data);
        if ($stdev == 0) {
            throw new InvalidDataInputException(
                "Z-scores are undefined when all values are identical (standard deviation is zero).",
            );
        }

        $zscores = [];
        foreach ($data as $value) {
            $zscores[] = Math::round(($value - $mean) / $stdev, $round);
        }

        return $zscores;
    }

    /**
     * Return values from the dataset that are outliers based on z-score threshold.
     * A value is considered an outlier if its absolute z-score exceeds the threshold.
     *
     * The default threshold of 3.0 is a common convention (values more than 3 standard
     * deviations from the mean).
     *
     * @param  array<int|float>  $data
     * @param  float  $threshold  absolute z-score threshold (default 3.0)
     * @return array<int|float> the outlier values
     *
     * @throws InvalidDataInputException if data size is less than 2 or stdev is zero
     */
    public static function outliers(array $data, float $threshold = 3.0): array
    {
        $zscores = self::zscores($data);
        $outliers = [];
        foreach ($data as $i => $value) {
            if (abs($zscores[$i]) > $threshold) {
                $outliers[] = $value;
            }
        }

        return $outliers;
    }

    /**
     * Return values from the dataset that are outliers based on the IQR method.
     * A value is an outlier if it falls below Q1 - factor * IQR or above Q3 + factor * IQR.
     *
     * This method is robust and does not assume a normal distribution, making it
     * suitable for skewed data. It is the same method used for box plot whiskers.
     *
     * @param  array<int|float>  $data
     * @param  float  $factor  IQR multiplier (default 1.5 for mild outliers, use 3.0 for extreme)
     * @return array<int|float> the outlier values
     *
     * @throws InvalidDataInputException if data has fewer than 2 elements
     */
    public static function iqrOutliers(array $data, float $factor = 1.5): array
    {
        $q1 = self::firstQuartile($data);
        $q3 = self::thirdQuartile($data);
        $iqr = $q3 - $q1;
        $lowerFence = $q1 - $factor * $iqr;
        $upperFence = $q3 + $factor * $iqr;

        $outliers = [];
        foreach ($data as $value) {
            if ($value < $lowerFence || $value > $upperFence) {
                $outliers[] = $value;
            }
        }

        return $outliers;
    }

    /**
     * Return the variance from the numeric data.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float the variance
     *
     * @throws InvalidDataInputException if data size is less than 2
     */
    public static function variance(
        array $data,
        ?int $round = null,
        int|float|null $xbar = null,
    ): float {
        $num_of_elements = self::count($data);
        if ($num_of_elements <= 1) {
            throw new InvalidDataInputException(
                "The data size must be greater than 1.",
            );
        }
        $sumSquareDifferences = 0.0;
        $average = $xbar ?? self::mean($data);

        foreach ($data as $i) {
            // sum of squares of differences between
            // all numbers and means.
            $sumSquareDifferences += ($i - $average) ** 2;
        }

        return Math::round(
            $sumSquareDifferences / ($num_of_elements - 1),
            $round,
        );
    }

    /**
     * Return the adjusted Fisher-Pearson sample skewness of the data.
     * This is the same formula used by Excel's SKEW() and scipy.stats.skew(bias=False).
     *
     * Formula: [n / ((n-1)(n-2))] * Σ((xi - x̄) / s)³
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float skewness
     *
     * @throws InvalidDataInputException if the data has fewer than 3 elements or all values are identical
     */
    public static function skewness(array $data, ?int $round = null): float
    {
        $n = self::count($data);
        if ($n < 3) {
            throw new InvalidDataInputException(
                "Skewness requires at least 3 data points.",
            );
        }

        $mean = self::mean($data);
        $stdev = self::stdev($data);

        if ($stdev == 0) {
            throw new InvalidDataInputException(
                "Skewness is undefined when all values are identical (standard deviation is zero).",
            );
        }

        $sumCubes = 0.0;
        foreach ($data as $xi) {
            $sumCubes += (($xi - $mean) / $stdev) ** 3;
        }

        $skewness = ($n / (($n - 1) * ($n - 2))) * $sumCubes;

        return Math::round($skewness, $round);
    }

    /**
     * Return the population (biased) skewness of the data.
     * This is the same formula used by scipy.stats.skew(bias=True).
     *
     * Formula: (1/n) * Σ((xi - x̄) / σ)³
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float population skewness
     *
     * @throws InvalidDataInputException if the data has fewer than 3 elements or all values are identical
     */
    public static function pskewness(array $data, ?int $round = null): float
    {
        $n = self::count($data);
        if ($n < 3) {
            throw new InvalidDataInputException(
                "Skewness requires at least 3 data points.",
            );
        }

        $mean = self::mean($data);
        $pstdev = self::pstdev($data);

        if ($pstdev == 0) {
            throw new InvalidDataInputException(
                "Skewness is undefined when all values are identical (standard deviation is zero).",
            );
        }

        $sumCubes = 0.0;
        foreach ($data as $xi) {
            $sumCubes += (($xi - $mean) / $pstdev) ** 3;
        }

        $pskewness = $sumCubes / $n;

        return Math::round($pskewness, $round);
    }

    /**
     * Return the excess kurtosis of the data using the sample formula.
     * This is the same formula used by Excel's KURT() and Python's
     * scipy.stats.kurtosis(bias=False, fisher=True).
     *
     * Excess kurtosis measures the "tailedness" of a distribution relative
     * to a normal distribution. A normal distribution has excess kurtosis 0.
     * Positive values (leptokurtic) indicate heavier tails and more outliers;
     * negative values (platykurtic) indicate lighter tails and fewer outliers.
     *
     * Formula: [n(n+1) / ((n-1)(n-2)(n-3))] * Σ((xi - x̄) / s)⁴ − [3(n-1)² / ((n-2)(n-3))]
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float excess kurtosis
     *
     * @throws InvalidDataInputException if the data has fewer than 4 elements or all values are identical
     */
    public static function kurtosis(array $data, ?int $round = null): float
    {
        $n = self::count($data);
        if ($n < 4) {
            throw new InvalidDataInputException(
                "Kurtosis requires at least 4 data points.",
            );
        }

        $mean = self::mean($data);
        $stdev = self::stdev($data);

        if ($stdev == 0) {
            throw new InvalidDataInputException(
                "Kurtosis is undefined when all values are identical (standard deviation is zero).",
            );
        }

        $sumFourth = 0.0;
        foreach ($data as $xi) {
            $sumFourth += (($xi - $mean) / $stdev) ** 4;
        }

        $kurtosis
            = (($n * ($n + 1)) / (($n - 1) * ($n - 2) * ($n - 3))) * $sumFourth
            - (3 * ($n - 1) ** 2) / (($n - 2) * ($n - 3));

        return Math::round($kurtosis, $round);
    }

    /**
     * Return the coefficient of variation (CV) of the data.
     * The coefficient of variation is the ratio of the standard deviation
     * to the mean, expressed as a percentage. It measures relative variability
     * and is useful for comparing dispersion across datasets with different units or scales.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @param  bool  $population if true, use population stdev/mean; otherwise sample
     * @return float the coefficient of variation as a percentage
     *
     * @throws InvalidDataInputException if the data has fewer than 2 elements (sample)
     *         or is empty (population), or if the mean is zero
     */
    public static function coefficientOfVariation(
        array $data,
        ?int $round = null,
        bool $population = false,
    ): float {
        $mean = self::mean($data);
        if ($mean == 0) {
            throw new InvalidDataInputException(
                "Coefficient of variation is undefined when the mean is zero.",
            );
        }

        $sd = $population ? self::pstdev($data) : self::stdev($data);

        return Math::round(($sd / abs($mean)) * 100, $round);
    }

    /**
     * Return the geometric mean of the numeric data.
     * That is the number that can replace each of these numbers so that their product
     * does not change.
     *
     * @param  array<int|float>  $data
     * @param  int|null  $round whether to round the result
     * @return float geometric mean
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function geometricMean(array $data, ?int $round = null): float
    {
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        $product = 1;
        foreach ($data as $value) {
            $product *= $value;
        }
        $geometricMean = $product ** (1 / $count);

        return Math::round($geometricMean, $round);
    }

    /**
     * Return the harmonic mean (the reciprocal of the arithmetic mean) of the numeric data.
     *
     * @param  array<int|float>  $data
     * @param mixed[]|null $weights additional weight to the elements (as if there were several of them)
     * @param  int|null  $round whether to round the result
     * @return float harmonic mean
     *
     * @throws InvalidDataInputException if the data is empty
     */
    public static function harmonicMean(
        array $data,
        ?array $weights = null,
        ?int $round = null,
    ): float {
        $sum = 0;
        $count = self::count($data);
        if ($count === 0) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        $sumWeigth = 0;
        foreach ($data as $key => $value) {
            if (!$value) {
                return 0;
            }
            $weight = is_null($weights) ? 1 : $weights[$key];
            $sumWeigth += $weight;
            $sum += $weight / $value;
        }

        return Math::round($sumWeigth / $sum, $round);
    }

    /**
     * Return the sample covariance of two inputs *$x* and *$y*.
     * Covariance is a measure of the joint variability of two inputs.
     *
     * @param  array<int|float>  $x
     * @param  array<int|float>  $y
     *
     * @throws InvalidDataInputException if 2 arrays have different size,
     * or if the length of arrays are < 2, or if the 2 input arrays has not numeric elements
     */
    public static function covariance(array $x, array $y): false|float
    {
        $countX = count($x);
        $countY = count($y);
        if ($countX !== $countY) {
            throw new InvalidDataInputException(
                "Covariance requires that both inputs have same number of data points.",
            );
        }
        if ($countX < 2) {
            throw new InvalidDataInputException(
                "Covariance requires at least two data points.",
            );
        }
        $meanX = self::mean($x);
        $meanY = self::mean($y);
        $add = 0.0;

        for ($pos = 0; $pos < $countX; $pos++) {
            $valueX = $x[$pos];
            if (!is_numeric($valueX)) {
                throw new InvalidDataInputException(
                    "Covariance requires numeric data points.",
                );
            }
            $valueY = $y[$pos];
            if (!is_numeric($valueY)) {
                throw new InvalidDataInputException(
                    "Covariance requires numeric data points.",
                );
            }
            $diffX = $valueX - $meanX;
            $diffY = $valueY - $meanY;
            $add += $diffX * $diffY;
        }

        // covariance for sample: N - 1
        return $add / (float) ($countX - 1);
    }

    /**
     * Return the Pearson’s correlation coefficient for two inputs.
     * Pearson’s correlation coefficient r takes values between -1 and +1.
     * It measures the strength and direction of the linear relationship,
     * where +1 means very strong, positive linear relationship,
     * -1 very strong, negative linear relationship,
     * and 0 no linear relationship.
     *
     * @param  array<int|float>  $x
     * @param  array<int|float>  $y
     *
     * @throws InvalidDataInputException if 2 arrays have different size,
     * or if the length of arrays are < 2, or if the 2 input arrays has not numeric elements,
     * or if the elements of the array are constants
     */
    public static function correlation(
        array $x,
        array $y,
        string $method = "linear",
    ): false|float {
        if ($method !== "linear" && $method !== "ranked") {
            throw new InvalidDataInputException(
                "Correlation method must be 'linear' or 'ranked'.",
            );
        }

        $countX = count($x);
        $countY = count($y);
        if ($countX !== $countY) {
            throw new InvalidDataInputException(
                "Correlation requires that both inputs have same number of data points.",
            );
        }
        if ($countX < 2) {
            throw new InvalidDataInputException(
                "Correlation requires at least two data points.",
            );
        }

        if ($method === "ranked") {
            $x = self::ranks($x);
            $y = self::ranks($y);
        }

        $meanX = self::mean($x);
        $meanY = self::mean($y);
        $a = 0;
        $bx = 0;
        $by = 0;
        $counter = count($x);
        for ($i = 0; $i < $counter; $i++) {
            $xr = $x[$i] - $meanX;
            $yr = $y[$i] - $meanY;
            $a += $xr * $yr;
            $bx += $xr ** 2;
            $by += $yr ** 2;
        }
        $b = sqrt($bx * $by);
        if ($b == 0) {
            throw new InvalidDataInputException(
                "Correlation, at least one of the inputs is constant.",
            );
        }

        return $a / $b;
    }

    /**
     * Assign average ranks to data values (handles ties by averaging).
     *
     * @param  array<int|float>  $data
     * @return array<float>
     */
    private static function ranks(array $data): array
    {
        $n = count($data);
        $indexed = [];
        for ($i = 0; $i < $n; $i++) {
            $indexed[] = [$data[$i], $i];
        }

        usort($indexed, fn(array $a, array $b): int => $a[0] <=> $b[0]);

        $ranks = array_fill(0, $n, 0.0);
        $i = 0;
        while ($i < $n) {
            $j = $i;
            while ($j < $n && $indexed[$j][0] === $indexed[$i][0]) {
                $j++;
            }
            $averageRank = ($i + 1 + $j) / 2.0;
            for ($k = $i; $k < $j; $k++) {
                $ranks[$indexed[$k][1]] = $averageRank;
            }
            $i = $j;
        }

        return $ranks;
    }

    /**
     * Create a continuous probability density function or cumulative distribution
     * function from discrete sample data using Kernel Density Estimation.
     *
     * Returns a Closure that estimates the density (or CDF) at any given point.
     *
     * @param  array<int|float>  $data  sample data
     * @param  float  $h  bandwidth (smoothing parameter), must be > 0
     * @param  KdeKernel  $kernel  kernel to use for estimation
     * @param  bool  $cumulative  if true, return CDF estimator; otherwise PDF estimator
     * @return \Closure  a callable that takes a float and returns the estimated density or CDF value
     *
     * @throws InvalidDataInputException if data is empty or bandwidth <= 0
     */
    public static function kde(
        array $data,
        float $h,
        KdeKernel $kernel = KdeKernel::Normal,
        bool $cumulative = false,
    ): \Closure {
        if ($data === []) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        if ($h <= 0) {
            throw new InvalidDataInputException(
                "Bandwidth h must be positive.",
            );
        }

        $kernel = $kernel->resolve();

        $sqrt2pi = sqrt(2.0 * M_PI);

        // Standard normal CDF using Abramowitz & Stegun approximation (7.1.26)
        $normalCdf = static function (float $t) use ($sqrt2pi): float {
            $negative = $t < 0;
            $t = abs($t);
            $b1 = 0.31938153;
            $b2 = -0.356563782;
            $b3 = 1.781477937;
            $b4 = -1.821255978;
            $b5 = 1.330274429;
            $p = 0.2316419;
            $k = 1.0 / (1.0 + $p * $t);
            $pdf = exp((-$t * $t) / 2.0) / $sqrt2pi;
            $cdf
                = 1.0
                - $pdf
                    * $k
                    * ($b1 + $k * ($b2 + $k * ($b3 + $k * ($b4 + $k * $b5))));

            return $negative ? 1.0 - $cdf : $cdf;
        };

        $kernels = [
            KdeKernel::Normal->value => [
                "pdf" => static fn(float $t): float => exp((-$t * $t) / 2.0)
                    / $sqrt2pi,
                "cdf" => $normalCdf,
                "support" => null,
            ],
            KdeKernel::Logistic->value => [
                "pdf" => static fn(float $t): float => 0.5 / (1.0 + cosh($t)),
                "cdf" => static fn(float $t): float => 1.0 / (1.0 + exp(-$t)),
                "support" => null,
            ],
            KdeKernel::Sigmoid->value => [
                "pdf" => static fn(float $t): float => 1.0 / M_PI / cosh($t),
                "cdf" => static fn(float $t): float => (2.0 / M_PI)
                    * atan(exp($t)),
                "support" => null,
            ],
            KdeKernel::Rectangular->value => [
                "pdf" => static fn(float $t): float => 0.5,
                "cdf" => static fn(float $t): float => 0.5 * $t + 0.5,
                "support" => 1.0,
            ],
            KdeKernel::Triangular->value => [
                "pdf" => static fn(float $t): float => 1.0 - abs($t),
                "cdf" => static fn(float $t): float => $t >= 0
                    ? 1.0 - ((1.0 - $t) * (1.0 - $t)) / 2.0
                    : ((1.0 + $t) * (1.0 + $t)) / 2.0,
                "support" => 1.0,
            ],
            KdeKernel::Parabolic->value => [
                "pdf" => static fn(float $t): float => 0.75 * (1.0 - $t * $t),
                "cdf" => static fn(float $t): float => -0.25 * $t * $t * $t
                    + 0.75 * $t
                    + 0.5,
                "support" => 1.0,
            ],
            KdeKernel::Quartic->value => [
                "pdf" => static fn(float $t): float => (15.0 / 16.0)
                    * (1.0 - $t * $t) ** 2,
                "cdf" => static fn(float $t): float => (15.0 * $t
                    - 10.0 * $t ** 3
                    + 3.0 * $t ** 5)
                    / 16.0
                    + 0.5,
                "support" => 1.0,
            ],
            KdeKernel::Triweight->value => [
                "pdf" => static fn(float $t): float => (35.0 / 32.0)
                    * (1.0 - $t * $t) ** 3,
                "cdf" => static fn(float $t): float => (35.0 * $t
                    - 35.0 * $t ** 3
                    + 21.0 * $t ** 5
                    - 5.0 * $t ** 7)
                    / 32.0
                    + 0.5,
                "support" => 1.0,
            ],
            KdeKernel::Cosine->value => [
                "pdf" => static fn(float $t): float => (M_PI / 4.0)
                    * cos((M_PI * $t) / 2.0),
                "cdf" => static fn(float $t): float => 0.5
                    * sin((M_PI * $t) / 2.0)
                    + 0.5,
                "support" => 1.0,
            ],
        ];

        $kernelDef = $kernels[$kernel->value]; // @phpstan-ignore offsetAccess.notFound
        $support = $kernelDef["support"];
        $fn = $cumulative ? $kernelDef["cdf"] : $kernelDef["pdf"];

        $sorted = $data;
        sort($sorted);
        $n = count($sorted);

        if ($cumulative) {
            return static function (float $x) use (
                $sorted,
                $n,
                $h,
                $fn,
                $support,
            ): float {
                $sum = 0.0;
                if ($support !== null) {
                    $lo = self::bisectLeft($sorted, $x - $h * $support);
                    $hi = self::bisectRight($sorted, $x + $h * $support);
                    for ($i = $lo; $i < $hi; $i++) {
                        $t = ($x - $sorted[$i]) / $h;
                        $sum += $fn($t);
                    }
                    // Points entirely to the left contribute 1.0 each
                    $sum += $lo;
                } else {
                    for ($i = 0; $i < $n; $i++) {
                        $t = ($x - $sorted[$i]) / $h;
                        $sum += $fn($t);
                    }
                }

                return $sum / $n;
            };
        }

        return static function (float $x) use (
            $sorted,
            $n,
            $h,
            $fn,
            $support,
        ): float {
            $sum = 0.0;
            if ($support !== null) {
                $lo = self::bisectLeft($sorted, $x - $h * $support);
                $hi = self::bisectRight($sorted, $x + $h * $support);
                for ($i = $lo; $i < $hi; $i++) {
                    $t = ($x - $sorted[$i]) / $h;
                    $sum += $fn($t);
                }
            } else {
                for ($i = 0; $i < $n; $i++) {
                    $t = ($x - $sorted[$i]) / $h;
                    $sum += $fn($t);
                }
            }

            return $sum / ($n * $h);
        };
    }

    /**
     * Generate random samples from a Kernel Density Estimate.
     *
     * Returns a Closure that, when called, produces a random float drawn
     * from the KDE distribution defined by the data and bandwidth.
     *
     * @param  array<int|float>  $data  sample data
     * @param  float  $h  bandwidth (smoothing parameter), must be > 0
     * @param  KdeKernel  $kernel  kernel to use for estimation
     * @param  int|null  $seed  optional seed for reproducibility
     * @return \Closure  a callable that returns a random float from the KDE
     *
     * @throws InvalidDataInputException if data is empty or bandwidth <= 0
     */
    public static function kdeRandom(
        array $data,
        float $h,
        KdeKernel $kernel = KdeKernel::Normal,
        ?int $seed = null,
    ): \Closure {
        if ($data === []) {
            throw new InvalidDataInputException("The data must not be empty.");
        }
        if ($h <= 0) {
            throw new InvalidDataInputException(
                "Bandwidth h must be positive.",
            );
        }

        $kernel = $kernel->resolve();

        // Acklam rational approximation for standard normal inverse CDF
        $normalInvCdf = static function (float $p): float {
            $a = [
                -3.969683028665376e1,
                2.209460984245205e2,
                -2.759285104469687e2,
                1.38357751867269e2,
                -3.066479806614716e1,
                2.506628277459239,
            ];
            $b = [
                -5.447609879822406e1,
                1.615858368580409e2,
                -1.556989798598866e2,
                6.680131188771972e1,
                -1.328068155288572e1,
            ];
            $c = [
                -7.784894002430293e-3,
                -3.223964580411365e-1,
                -2.400758277161838,
                -2.549732539343734,
                4.374664141464968,
                2.938163982698783,
            ];
            $d = [
                7.784695709041462e-3,
                3.224671290700398e-1,
                2.445134137142996,
                3.754408661907416,
            ];

            $pLow = 0.02425;
            $pHigh = 1.0 - $pLow;
            if ($p < $pLow) {
                $q = sqrt(-2.0 * log($p));
                return ((((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3])
                    * $q
                    + $c[4])
                    * $q
                    + $c[5])
                    / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q
                        + 1.0);
            }

            if ($p <= $pHigh) {
                $q = $p - 0.5;
                $r = $q * $q;
                return (((((($a[0] * $r + $a[1]) * $r + $a[2]) * $r + $a[3])
                    * $r
                    + $a[4])
                    * $r
                    + $a[5])
                    * $q)
                    / ((((($b[0] * $r + $b[1]) * $r + $b[2]) * $r + $b[3]) * $r
                        + $b[4])
                        * $r
                        + 1.0);
            }
            $q = sqrt(-2.0 * log(1.0 - $p));
            return -(
                (((($c[0] * $q + $c[1]) * $q + $c[2]) * $q + $c[3]) * $q
                    + $c[4])
                    * $q
                + $c[5]
            )
                / (((($d[0] * $q + $d[1]) * $q + $d[2]) * $q + $d[3]) * $q + 1.0);
        };

        // Newton-Raphson solver for kernels without closed-form inverse CDF
        $newtonRaphson = static function (
            float $p,
            callable $cdf,
            callable $pdf,
            float $x0,
        ): float {
            $x = $x0;
            for ($i = 0; $i < 100; $i++) {
                $err = $cdf($x) - $p;
                if (abs($err) <= 1e-12) {
                    break;
                }
                $x -= $err / $pdf($x);
            }
            return $x;
        };

        // Quartic CDF and PDF for Newton-Raphson
        $quarticCdf = static fn(float $t): float => $t <= -1.0
            ? 0.0
            : ($t >= 1.0
                ? 1.0
                : (15.0 * $t - 10.0 * $t ** 3 + 3.0 * $t ** 5) / 16.0 + 0.5);
        $quarticPdf = static fn(float $t): float => $t < -1.0 || $t > 1.0
            ? 0.0
            : (15.0 / 16.0) * (1.0 - $t * $t) ** 2;

        // Triweight CDF and PDF for Newton-Raphson
        $triweightCdf = static fn(float $t): float => $t <= -1.0
            ? 0.0
            : ($t >= 1.0
                ? 1.0
                : (35.0 * $t
                        - 35.0 * $t ** 3
                        + 21.0 * $t ** 5
                        - 5.0 * $t ** 7)
                        / 32.0
                    + 0.5);
        $triweightPdf = static fn(float $t): float => $t < -1.0 || $t > 1.0
            ? 0.0
            : (35.0 / 32.0) * (1.0 - $t * $t) ** 3;

        $invcdfMap = [
            KdeKernel::Normal->value => $normalInvCdf,
            KdeKernel::Logistic->value => static fn(float $p): float => log(
                $p / (1.0 - $p),
            ),
            KdeKernel::Sigmoid->value => static fn(float $p): float => log(
                tan(($p * M_PI) / 2.0),
            ),
            KdeKernel::Rectangular->value => static fn(float $p): float => 2.0
                * $p
                - 1.0,
            KdeKernel::Triangular->value => static fn(float $p): float => $p
            < 0.5
                ? sqrt(2.0 * $p) - 1.0
                : 1.0 - sqrt(2.0 - 2.0 * $p),
            KdeKernel::Parabolic->value => static fn(float $p): float => 2.0
                * cos((acos(2.0 * $p - 1.0) + M_PI) / 3.0),
            KdeKernel::Quartic->value => static function (float $p) use (
                $newtonRaphson,
                $quarticCdf,
                $quarticPdf,
            ): float {
                if ($p <= 0.5) {
                    $sign = 1.0;
                } else {
                    $sign = -1.0;
                    $p = 1.0 - $p;
                }
                if ($p < 0.0106) {
                    $x = (2.0 * $p) ** 0.3838 - 1.0;
                } else {
                    $x = (2.0 * $p) ** 0.4258865685331 - 1.0;
                    if ($p < 0.499) {
                        $x
                            += 0.026818732
                            * sin(7.101753784 * $p + 2.73230839482953);
                    }
                }
                $x *= $sign;
                return $newtonRaphson(
                    $sign === 1.0 ? $p : 1.0 - $p,
                    $quarticCdf,
                    $quarticPdf,
                    $x,
                );
            },
            KdeKernel::Triweight->value => static function (float $p) use (
                $newtonRaphson,
                $triweightCdf,
                $triweightPdf,
            ): float {
                if ($p <= 0.5) {
                    $sign = 1.0;
                } else {
                    $sign = -1.0;
                    $p = 1.0 - $p;
                }
                $x = (2.0 * $p) ** 0.3400218741872791 - 1.0;
                if ($p > 0.00001 && $p < 0.499) {
                    $x -= 0.033 * sin(1.07 * 2.0 * M_PI * ($p - 0.035));
                }
                $x *= $sign;
                return $newtonRaphson(
                    $sign === 1.0 ? $p : 1.0 - $p,
                    $triweightCdf,
                    $triweightPdf,
                    $x,
                );
            },
            KdeKernel::Cosine->value => static fn(float $p): float => (2.0
                / M_PI)
                * asin(2.0 * $p - 1.0),
        ];

        $invcdf = $invcdfMap[$kernel->value]; // @phpstan-ignore offsetAccess.notFound
        $n = count($data);

        if ($seed !== null) {
            mt_srand($seed);
        }

        return static function () use ($data, $n, $h, $invcdf): float {
            $i = mt_rand(0, $n - 1);
            $u = mt_rand(1, mt_getrandmax()) / mt_getrandmax();
            return $data[$i] + $h * $invcdf($u);
        };
    }

    /**
     * @param  array<int|float>  $x
     * @param  array<int|float>  $y
     * @return array<int|float>
     *
     * @throws InvalidDataInputException if 2 arrays have different size,
     * or if the length of arrays are < 2, or if the 2 input arrays has not numeric elements,
     * or if the elements of the array are constants
     */
    public static function linearRegression(
        array $x,
        array $y,
        bool $proportional = false,
    ): array {
        $countX = count($x);
        $countY = count($y);
        if ($countX !== $countY) {
            throw new InvalidDataInputException(
                "Linear regression requires that both inputs have same number of data points.",
            );
        }
        if ($countX < 2) {
            throw new InvalidDataInputException(
                "Linear regression requires at least two data points.",
            );
        }
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXX = 0;
        $sumXY = 0;

        foreach ($x as $key => $value) {
            $sumXY += $value * $y[$key];
            $sumXX += $value * $value;
        }

        if ($proportional) {
            if ($sumXX == 0) {
                throw new InvalidDataInputException(
                    "Proportional linear regression requires x values that are not all zeros.",
                );
            }
            $slope = (float) ($sumXY / $sumXX);

            return [$slope, 0.0];
        }

        $denominator = $countX * $sumXX - $sumX * $sumX;
        if ($denominator === 0) {
            throw new InvalidDataInputException(
                "Linear regression, the inputs is constant.",
            );
        }
        $slope = ($countX * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $countX;

        return [$slope, $intercept];
    }

    /**
     * Calculate the coefficient of determination (R²).
     *
     * R² measures the proportion of variance in y explained by the
     * linear regression on x. Returns a value between 0 and 1.
     *
     * @param  array<int|float>  $x
     * @param  array<int|float>  $y
     * @throws InvalidDataInputException
     */
    public static function rSquared(array $x, array $y, bool $proportional = false, ?int $round = null): float
    {
        $countX = count($x);
        $countY = count($y);

        if ($countX !== $countY) {
            throw new InvalidDataInputException(
                "R-squared requires x and y arrays of the same length.",
            );
        }

        if ($countX < 2) {
            throw new InvalidDataInputException(
                "R-squared requires at least 2 data points.",
            );
        }

        [$slope, $intercept] = self::linearRegression($x, $y, $proportional);
        $meanY = self::mean($y);

        $ssRes = 0.0;
        $ssTot = 0.0;

        foreach ($y as $key => $yi) {
            $predicted = $slope * $x[$key] + $intercept;
            $ssRes += ($yi - $predicted) ** 2;
            $ssTot += ($yi - $meanY) ** 2;
        }

        if ($ssTot == 0) {
            throw new InvalidDataInputException(
                "R-squared is undefined when y values are constant (zero variance).",
            );
        }

        $rSquared = 1 - $ssRes / $ssTot;

        if ($round !== null) {
            return round($rSquared, $round);
        }

        return $rSquared;
    }

    /**
     * Return the confidence interval for the mean using the normal (z) distribution.
     *
     * Computes: mean ± z * (stdev / √n)
     *
     * @param  array<int|float>  $data
     * @param  float  $confidenceLevel the confidence level (e.g. 0.95 for 95%)
     * @param  int|null  $round whether to round the result
     * @return array{0: float, 1: float} [lower bound, upper bound]
     *
     * @throws InvalidDataInputException if data has fewer than 2 elements or confidence level is not in (0, 1)
     */
    public static function confidenceInterval(
        array $data,
        float $confidenceLevel = 0.95,
        ?int $round = null,
    ): array {
        if (self::count($data) < 2) {
            throw new InvalidDataInputException(
                "Confidence interval requires at least 2 data points.",
            );
        }

        if ($confidenceLevel <= 0.0 || $confidenceLevel >= 1.0) {
            throw new InvalidDataInputException(
                "Confidence level must be between 0 and 1 exclusive.",
            );
        }

        $mean = self::mean($data);
        $standardError = self::sem($data);

        $zCritical = (new NormalDist(0.0, 1.0))->invCdf((1 + $confidenceLevel) / 2);
        $margin = $zCritical * $standardError;

        $lower = $mean - $margin;
        $upper = $mean + $margin;

        if ($round !== null) {
            return [Math::round($lower, $round), Math::round($upper, $round)];
        }

        return [$lower, $upper];
    }

    /**
     * Perform a one-sample Z-test for the mean.
     *
     * Tests whether the sample mean differs significantly from a known
     * population mean using the normal distribution.
     *
     * @param  array<int|float>  $data
     * @param  float  $populationMean  the hypothesized population mean
     * @param  Alternative  $alternative  the alternative hypothesis
     * @param  int|null  $round  whether to round the results
     * @return array{zScore: float, pValue: float}
     *
     * @throws InvalidDataInputException if data has fewer than 2 elements
     */
    public static function zTest(
        array $data,
        float $populationMean,
        Alternative $alternative = Alternative::TwoSided,
        ?int $round = null,
    ): array {
        if (self::count($data) < 2) {
            throw new InvalidDataInputException(
                "Z-test requires at least 2 data points.",
            );
        }

        $zScore = (self::mean($data) - $populationMean) / self::sem($data);

        $normalDist = new NormalDist(0.0, 1.0);

        $pValue = match ($alternative) {
            Alternative::TwoSided => 2 * (1 - $normalDist->cdf(abs($zScore))),
            Alternative::Greater => 1 - $normalDist->cdf($zScore),
            Alternative::Less => $normalDist->cdf($zScore),
        };

        return [
            'zScore' => Math::round($zScore, $round),
            'pValue' => Math::round($pValue, $round),
        ];
    }
}
