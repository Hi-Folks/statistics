<p align="center">
    <img src="https://repository-images.githubusercontent.com/445609326/e2539776-0f8f-4556-be1d-887ea2368813" alt="PHP package for Statistics">
</p>

<h1 align="center">
    Statistics PHP package
</h1>

<p align=center>
    <a href="https://packagist.org/packages/hi-folks/statistics">
        <img src="https://img.shields.io/packagist/v/hi-folks/statistics.svg?style=for-the-badge" alt="Latest Version on Packagist">
    </a>
    <a href="https://packagist.org/packages/hi-folks/statistics">
        <img src="https://img.shields.io/packagist/dt/hi-folks/statistics.svg?style=for-the-badge" alt="Total Downloads">
    </a>
    <br>
    <a href="https://github.com/Hi-Folks/statistics/blob/main/.github/workflows/static-code-analysis.yml">
        <img src="https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=for-the-badge" alt="Static Code analysis">
    </a>
    <img src="https://img.shields.io/packagist/l/hi-folks/statistics?style=for-the-badge" alt="Packagist License">
    <br>
    <img src="https://img.shields.io/packagist/php-v/hi-folks/statistics?style=for-the-badge" alt="Packagist PHP Version Support">
    <img src="https://img.shields.io/github/last-commit/hi-folks/statistics?style=for-the-badge" alt="GitHub last commit">
</p>

<p align=center>
    <a href="https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml">
        <img src="https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml/badge.svg?branch=main&style=for-the-badge" alt="Tests">
    </a>
</p>

<p align=center>
    <i>
        Introducing a PHP package enabling comprehensive mathematical statistics calculations on numeric data.
    </i>
</p>

I've put together a package of useful statistical functions.

These functions originally stemmed from my exploration of FIT files, which contain a wealth of data about sports activities. Within these files, you can find detailed information on metrics such as Heart Rate, Speed, Cadence, Power, and more. I developed these statistical functions to help gain deeper insights into the numerical data and performance of these sports activities.

The functions provided by this package, cover a range of measures, including mean, mode, median, range, quantiles, first quartile (25th percentile), third quartile (75th percentile), frequency tables (both cumulative and relative), standard deviation (applicable to both populations and samples), and variance (once again, for populations and samples).

> This package is inspired by the [Python statistics module](https://docs.python.org/3/library/statistics.html)

## Installation

You can install the package via composer:

```bash
composer require hi-folks/statistics
```

## Usage

### Stat class

Stat class has methods to calculate an average or typical value from a population or sample.
This class provides methods for calculating mathematical statistics of numeric data.
The various mathematical statistics are listed below:


| Mathematical Statistic | Description |
| ---------------------- | ----------- |
| `mean()` | arithmetic mean or "average" of data |
| `fmean()` | floating-point arithmetic mean, with optional weighting and precision |
| `trimmedMean()` | trimmed (truncated) mean — mean after removing outliers from each side |
| `median()` | median or "middle value" of data |
| `medianLow()` | low median of data |
| `medianHigh()` | high median of data |
| `medianGrouped()` | median of grouped data, using interpolation |
| `mode()` | single mode (most common value) of discrete or nominal data |
| `multimode()` | list of modes (most common values) of discrete or nominal data |
| `quantiles()` | cut points dividing the range of a probability distribution into continuous intervals with equal probabilities (supports `exclusive` and `inclusive` methods) |
| `thirdQuartile()` | 3rd quartile, is the value at which 75 percent of the data is below it |
| `firstQuartile()` | first quartile, is the value at which 25 percent of the data is below it |
| `percentile()` | value at any percentile (0–100) with linear interpolation |
| `pstdev()` | Population standard deviation |
| `stdev()` | Sample standard deviation |
| `pvariance()` | variance for a population (supports pre-computed mean via `mu`) |
| `variance()` | variance for a sample (supports pre-computed mean via `xbar`) |
| `skewness()` | adjusted Fisher-Pearson sample skewness |
| `pskewness()` | population (biased) skewness |
| `kurtosis()` | excess kurtosis (sample formula, 0 for normal distribution) |
| `coefficientOfVariation()` | coefficient of variation (CV%), relative dispersion as percentage |
| `geometricMean()` | geometric mean |
| `harmonicMean()` | harmonic mean |
| `correlation()` | Pearson’s or Spearman’s rank correlation coefficient for two inputs |
| `covariance()` | the sample covariance of two inputs |
| `linearRegression()` | return the slope and intercept of simple linear regression parameters estimated using ordinary least squares (supports `proportional: true` for regression through the origin) |
| `kde()` | kernel density estimation — returns a closure that estimates the probability density (or CDF) at any point |
| `kdeRandom()` | random sampling from a kernel density estimate — returns a closure that generates random floats from the KDE distribution |

#### Stat::mean( array $data )
Return the sample arithmetic mean of the array _$data_.
The arithmetic mean is the sum of the data divided by the number of data points. It is commonly called “the average”, although it is only one of many mathematical averages. It is a measure of the central location of the data.

```php
use HiFolks\Statistics\Stat;
$mean = Stat::mean([1, 2, 3, 4, 4]);
// 2.8
$mean = Stat::mean([-1.0, 2.5, 3.25, 5.75]);
// 2.625
```

#### Stat::fmean( array $data, array|null $weights = null, int|null $precision = null )
Return the arithmetic mean of the array `$data`, as a float, with optional weights and precision control.
This function behaves like `mean()` but ensures a floating-point result and supports weighted datasets.
If `$weights` is provided, it computes the weighted average. The result is rounded to a given decimal $precision.
The result is rounded to `$precision` decimal places. 
If `$precision` is null, no rounding is applied — this may lead to results with long or unexpected decimal expansions due to the nature of floating-point arithmetic in PHP. Using rounding helps ensure cleaner, more predictable output.

```php 
use HiFolks\Statistics\Stat;

// Unweighted mean (same as mean but always float)
$fmean = Stat::fmean([3.5, 4.0, 5.25]);
// 4.25

// Weighted mean
$fmean = Stat::fmean([3.5, 4.0, 5.25], [1, 2, 1]);
// 4.1875

// Custom precision
$fmean = Stat::fmean([3.5, 4.0, 5.25], null, 2);
// 4.25

$fmean = Stat::fmean([3.5, 4.0, 5.25], [1, 2, 1], 3);
// 4.188

```

If the input is empty, or weights are invalid (e.g., length mismatch or sum is zero), an exception is thrown.
Use this function when you need floating-point accuracy or to apply custom weighting and rounding to your average.

#### Stat::trimmedMean( array $data, float $proportionToCut = 0.1, ?int $round = null )
Return the trimmed (truncated) mean of the data. Computes the mean after removing the lowest and highest fraction of values. This is a robust measure of central tendency, less sensitive to outliers than the regular mean.

The `$proportionToCut` parameter specifies the fraction to trim from **each** side (must be in the range `[0, 0.5)`). For example, `0.1` removes the bottom 10% and top 10%.

```php
use HiFolks\Statistics\Stat;
$mean = Stat::trimmedMean([1, 2, 3, 4, 5, 6, 7, 8, 9, 100], 0.1);
// 5.5 (outlier 100 and lowest value 1 removed)

$mean = Stat::trimmedMean([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 0.2);
// 5.5 (removes 2 values from each side)

$mean = Stat::trimmedMean([1, 2, 3, 4, 5], 0.0);
// 3.0 (no trimming, same as regular mean)
```

#### Stat::geometricMean( array $data )
The geometric mean indicates the central tendency or typical value of the data using the product of the values (as opposed to the arithmetic mean which uses their sum).

```php
use HiFolks\Statistics\Stat;
$mean = Stat::geometricMean([54, 24, 36], 1);
// 36.0
```
#### Stat::harmonicMean( array $data )
The harmonic mean is the reciprocal of the arithmetic mean() of the reciprocals of the data. For example, the harmonic mean of three values a, b, and c will be equivalent to 3/(1/a + 1/b + 1/c). If one of the values is zero, the result will be zero.

```php
use HiFolks\Statistics\Stat;
$mean = Stat::harmonicMean([40, 60], null, 1);
// 48.0
```

You can also calculate the harmonic weighted mean.
Suppose a car travels 40 km/hr for 5 km, and when traffic clears, speeds up to 60 km/hr for the remaining 30 km of the journey. What is the average speed?

```php
use HiFolks\Statistics\Stat;
Stat::harmonicMean([40, 60], [5, 30], 1);
// 56.0
```
where:
- 40, 60:  are the elements
- 5, 30: are the weights for each element (the first weight is the weight of the first element, the second one is the weight of the second element)
- 1: is the decimal numbers you want to round


#### Stat::median( array $data )
Return the median (middle value) of numeric data, using the common “mean of middle two” method.

```php
use HiFolks\Statistics\Stat;
$median = Stat::median([1, 3, 5]);
// 3
$median = Stat::median([1, 3, 5, 7]);
// 4
```

#### Stat::medianLow( array $data )
Return the low median of numeric data.
The low median is always a member of the data set. When the number of data points is odd, the middle value is returned. When it is even, the smaller of the two middle values is returned.

```php
use HiFolks\Statistics\Stat;
$median = Stat::medianLow([1, 3, 5]);
// 3
$median = Stat::medianLow([1, 3, 5, 7]);
// 3
```



#### Stat::medianHigh( array $data )
Return the high median of data.
The high median is always a member of the data set. When the number of data points is odd, the middle value is returned. When it is even, the larger of the two middle values is returned.

```php
use HiFolks\Statistics\Stat;
$median = Stat::medianHigh([1, 3, 5]);
// 3
$median = Stat::medianHigh([1, 3, 5, 7]);
// 5
```

#### Stat::medianGrouped( array $data, float $interval = 1.0 )
Estimate the median for numeric data that has been grouped or binned around the midpoints of consecutive, fixed-width intervals.
The `$interval` parameter specifies the width of each bin (default `1.0`). This function uses interpolation within the median interval, assuming values are evenly distributed across each bin.

```php
use HiFolks\Statistics\Stat;
$median = Stat::medianGrouped([1, 2, 2, 3, 4, 4, 4, 4, 4, 5]);
// 3.7
$median = Stat::medianGrouped([1, 3, 3, 5, 7]);
// 3.25
$median = Stat::medianGrouped([1, 3, 3, 5, 7], 2);
// 3.5
```

For example, demographic data summarized into ten-year age groups:
```php
use HiFolks\Statistics\Stat;
// 172 people aged 20-30, 484 aged 30-40, 387 aged 40-50, etc.
$data = array_merge(
    array_fill(0, 172, 25),
    array_fill(0, 484, 35),
    array_fill(0, 387, 45),
    array_fill(0, 22, 55),
    array_fill(0, 6, 65),
);
round(Stat::medianGrouped($data, 10), 1);
// 37.5
```

#### Stat::quantiles( array $data, $n=4, $round=null, $method='exclusive'  )
Divide data into n continuous intervals with equal probability. Returns a list of n - 1 cut points separating the intervals.
Set n to 4 for quartiles (the default). Set n to 10 for deciles. Set n to 100 for percentiles which gives the 99 cut points that separate data into 100 equal-sized groups.

The `$method` parameter controls the interpolation method:
- `'exclusive'` (default): uses `m = count + 1`. Suitable for sampled data that may have more extreme values beyond the sample.
- `'inclusive'`: uses `m = count - 1`. Suitable for population data or samples known to include the most extreme values. The minimum value is treated as the 0th percentile and the maximum as the 100th percentile.


```php
use HiFolks\Statistics\Stat;
$quantiles = Stat::quantiles([98, 90, 70,18,92,92,55,83,45,95,88]);
// [ 55.0, 88.0, 92.0 ]
$quantiles = Stat::quantiles([105, 129, 87, 86, 111, 111, 89, 81, 108, 92, 110,100, 75, 105, 103, 109, 76, 119, 99, 91, 103, 129,106, 101, 84, 111, 74, 87, 86, 103, 103, 106, 86,111, 75, 87, 102, 121, 111, 88, 89, 101, 106, 95,103, 107, 101, 81, 109, 104], 10);
// [81.0, 86.2, 89.0, 99.4, 102.5, 103.6, 106.0, 109.8, 111.0]

// Inclusive method
$quantiles = Stat::quantiles([1, 2, 3, 4, 5], method: 'inclusive');
// [2.0, 3.0, 4.0]
```
#### Stat::firstQuartile( array $data, $round=null  )
The lower quartile, or first quartile (Q1), is the value under which 25% of data points are found when they are arranged in increasing order.

```php
use HiFolks\Statistics\Stat;
$percentile = Stat::firstQuartile([98, 90, 70,18,92,92,55,83,45,95,88]);
// 55.0
```

#### Stat::thirdQuartile( array $data, $round=null  )
The upper quartile, or third quartile (Q3), is the value under which 75% of data points are found when arranged in increasing order.

```php
use HiFolks\Statistics\Stat;
$percentile = Stat::thirdQuartile([98, 90, 70,18,92,92,55,83,45,95,88]);
// 92.0
```

#### Stat::percentile( array $data, float $p, ?int $round = null )
Return the value at the given percentile of the data, using linear interpolation between adjacent data points (exclusive method, consistent with `quantiles()`).

The percentile `$p` must be between 0 and 100. Requires at least 2 data points.

```php
use HiFolks\Statistics\Stat;
$value = Stat::percentile([10, 20, 30, 40, 50, 60, 70, 80, 90, 100], 50);
// 55.0 (median)

$value = Stat::percentile([10, 20, 30, 40, 50, 60, 70, 80, 90, 100], 90);
// 91.0
```

#### Stat::pstdev( array $data )
Return the **Population** Standard Deviation, a measure of the amount of variation or dispersion of a set of values.
A low standard deviation indicates that the values tend to be close to the mean of the set, while a high standard deviation indicates that the values are spread out over a wider range.

```php
use HiFolks\Statistics\Stat;
$stdev = Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75]);
// 0.986893273527251
$stdev = Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75], 4);
// 0.9869
```

#### Stat::stdev( array $data )
Return the **Sample** Standard Deviation, a measure of the amount of variation or dispersion of a set of values.
A low standard deviation indicates that the values tend to be close to the mean of the set, while a high standard deviation indicates that the values are spread out over a wider range.

```php
use HiFolks\Statistics\Stat;
$stdev = Stat::stdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75]);
// 1.0810874155219827
$stdev = Stat::stdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75], 4);
// 1.0811
```

#### Stat::variance ( array $data, ?int $round = null, int|float|null $xbar = null)
Variance is a measure of dispersion of data points from the mean.
Low variance indicates that data points are generally similar and do not vary widely from the mean.
High variance indicates that data values have greater variability and are more widely dispersed from the mean.

To calculate the variance from a *sample*:
```php
use HiFolks\Statistics\Stat;
$variance = Stat::variance([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5]);
// 1.3720238095238095
```

If you have already computed the mean, you can pass it via `xbar` to avoid recalculation:
```php
$data = [2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5];
$mean = Stat::mean($data);
$variance = Stat::variance($data, xbar: $mean);
```

If you need to calculate the variance on the whole population and not just on a sample you need to use *pvariance* method. You can optionally pass the population mean via `mu`:
```php
use HiFolks\Statistics\Stat;
$variance = Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25]);
// 1.25

// With pre-computed mean:
$data = [0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25];
$mu = Stat::mean($data);
$variance = Stat::pvariance($data, mu: $mu);
```


#### Stat::skewness ( array $data, ?int $round = null )
Skewness is a measure of the asymmetry of a distribution. The adjusted Fisher-Pearson formula is used, which is the same as Excel's `SKEW()` and Python's `scipy.stats.skew(bias=False)`.

A positive skewness indicates a right-skewed distribution (tail extends to the right), while a negative skewness indicates a left-skewed distribution. A symmetric distribution has a skewness of 0.

Requires at least 3 data points.

```php
use HiFolks\Statistics\Stat;
$skewness = Stat::skewness([1, 2, 3, 4, 5]);
// 0.0 (symmetric)

$skewness = Stat::skewness([1, 1, 1, 1, 1, 10]);
// positive (right-skewed)
```

If you need the population (biased) skewness instead of the sample skewness, use `pskewness()`. This is equivalent to `scipy.stats.skew(bias=True)`:
```php
use HiFolks\Statistics\Stat;
$pskewness = Stat::pskewness([1, 1, 1, 1, 1, 10]);
```

#### Stat::kurtosis ( array $data, ?int $round = null )
Kurtosis measures the "tailedness" of a distribution — how much data lives in the extreme tails compared to a normal distribution. This method returns the **excess kurtosis** using the sample formula, which is the same as Excel's `KURT()` and Python's `scipy.stats.kurtosis(bias=False)`.

A normal distribution has excess kurtosis of 0. Positive values (leptokurtic) indicate heavier tails and more outliers. Negative values (platykurtic) indicate lighter tails and fewer outliers.

Requires at least 4 data points.

```php
use HiFolks\Statistics\Stat;
$kurtosis = Stat::kurtosis([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
// negative (platykurtic, lighter tails than normal)

$kurtosis = Stat::kurtosis([1, 2, 2, 2, 2, 2, 2, 2, 2, 50]);
// positive (leptokurtic, heavier tails due to outlier)
```

#### Stat::coefficientOfVariation( array $data, ?int $round = null, bool $population = false )
The coefficient of variation (CV) is the ratio of the standard deviation to the mean, expressed as a percentage. It measures relative variability and is useful for comparing dispersion across datasets with different units or scales.

By default it uses the sample standard deviation. Pass `population: true` to use the population standard deviation instead.

Requires at least 2 data points (sample) or 1 (population). Throws if the mean is zero.

```php
use HiFolks\Statistics\Stat;
$cv = Stat::coefficientOfVariation([10, 20, 30, 40, 50]);
// ~52.70 (sample)

$cv = Stat::coefficientOfVariation([10, 20, 30, 40, 50], round: 2);
// 52.7

$cv = Stat::coefficientOfVariation([10, 20, 30, 40, 50], population: true);
// ~47.14 (population)
```

#### Stat::covariance ( array $x , array $y )
Covariance, static method, returns the sample covariance of two inputs *$x* and *$y*.
Covariance is a measure of the joint variability of two inputs.

```php
$covariance = Stat::covariance(
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [1, 2, 3, 1, 2, 3, 1, 2, 3]
);
// 0.75
```

```php
$covariance = Stat::covariance(
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [9, 8, 7, 6, 5, 4, 3, 2, 1]
);
// -7.5
```

#### Stat::correlation ( array $x , array $y, string $method = ‘linear’ )
Return the Pearson’s correlation coefficient for two inputs. Pearson’s correlation coefficient r takes values between -1 and +1. It measures the strength and direction of the linear relationship, where +1 means very strong, positive linear relationship, -1 very strong, negative linear relationship, and 0 no linear relationship.

Use `$method = ‘ranked’` for Spearman’s rank correlation, which measures monotonic relationships (not just linear). Spearman’s correlation is computed by applying Pearson’s formula to the ranks of the data.

```php
$correlation = Stat::correlation(
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [1, 2, 3, 4, 5, 6, 7, 8, 9]
);
// 1.0
```

```php
$correlation = Stat::correlation(
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [9, 8, 7, 6, 5, 4, 3, 2, 1]
);
// -1.0
```

Spearman’s rank correlation (non-linear but monotonic relationship):
```php
$correlation = Stat::correlation(
    [1, 2, 3, 4, 5],
    [1, 4, 9, 16, 25],
    ‘ranked’
);
// 1.0
```

#### Stat::linearRegression ( array $x , array $y , bool $proportional = false )
Return the slope and intercept of simple linear regression  parameters estimated using ordinary least squares.
Simple linear regression describes the relationship between an independent variable *$x* and a dependent variable *$y* in terms of a linear function.

```php
$years = [1971, 1975, 1979, 1982, 1983];
$films_total = [1, 2, 3, 4, 5]
list($slope, $intercept) = Stat::linearRegression(
    $years,
    $films_total
);
// 0.31
// -610.18
```
What happens in 2022, according to the samples above?

```php
round($slope * 2022 + $intercept);
// 17.0
```

When `proportional` is `true`, the regression line is forced through the origin (intercept = 0). This is useful when the relationship between *$x* and *$y* is known to be proportional:

```php
list($slope, $intercept) = Stat::linearRegression(
    [1, 2, 3, 4, 5],
    [2, 4, 6, 8, 10],
    proportional: true,
);
// $slope = 2.0
// $intercept = 0.0
```

#### Stat::kde ( array $data , float $h , KdeKernel $kernel = KdeKernel::Normal , bool $cumulative = false )
Create a continuous probability density function (or cumulative distribution function) from discrete sample data using Kernel Density Estimation.
Returns a `Closure` that can be called with any point to estimate the density (or CDF value).

Supported kernels: `KdeKernel::Normal` (alias `KdeKernel::Gauss`), `KdeKernel::Logistic`, `KdeKernel::Sigmoid`, `KdeKernel::Rectangular` (alias `KdeKernel::Uniform`), `KdeKernel::Triangular`, `KdeKernel::Parabolic` (alias `KdeKernel::Epanechnikov`), `KdeKernel::Quartic` (alias `KdeKernel::Biweight`), `KdeKernel::Triweight`, `KdeKernel::Cosine`.

```php
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Enums\KdeKernel;

$data = [-2.1, -1.3, -0.4, 1.9, 5.1, 6.2];
$f = Stat::kde($data, h: 1.5);
$f(2.5);
// estimated density at x = 2.5
```

Using a different kernel:

```php
$f = Stat::kde($data, h: 1.5, kernel: KdeKernel::Triangular);
$f(2.5);
```

Cumulative distribution function:

```php
$F = Stat::kde($data, h: 1.5, cumulative: true);
$F(2.5);
// estimated CDF at x = 2.5 (probability that a value is <= 2.5)
```

#### Stat::kdeRandom ( array $data , float $h , KdeKernel $kernel = KdeKernel::Normal , ?int $seed = null )
Generate random samples from a Kernel Density Estimate.
Returns a `Closure` that, when called, produces a random float drawn from the KDE distribution defined by the data and bandwidth.

Supported kernels: `KdeKernel::Normal` (alias `KdeKernel::Gauss`), `KdeKernel::Logistic`, `KdeKernel::Sigmoid`, `KdeKernel::Rectangular` (alias `KdeKernel::Uniform`), `KdeKernel::Triangular`, `KdeKernel::Parabolic` (alias `KdeKernel::Epanechnikov`), `KdeKernel::Quartic` (alias `KdeKernel::Biweight`), `KdeKernel::Triweight`, `KdeKernel::Cosine`.

```php
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Enums\KdeKernel;

$data = [-2.1, -1.3, -0.4, 1.9, 5.1, 6.2];
$rand = Stat::kdeRandom($data, h: 1.5, seed: 8675309);
$samples = [];
for ($i = 0; $i < 10; $i++) {
    $samples[] = round($rand(), 1);
}
// [2.5, 3.3, -1.8, 7.3, -2.1, 4.6, 4.4, 5.9, -3.2, -1.6]
```

Using a different kernel:

```php
$rand = Stat::kdeRandom($data, h: 1.5, kernel: KdeKernel::Triangular, seed: 42);
$rand();
```

### Freq class
With *Statistics* package you can calculate frequency table.
A frequency table lists the frequency of various outcomes in a sample.
Each entry in the table contains the frequency or count of the occurrences of values within a particular group or interval.


#### Freq::frequencies( array $data )
```php
use HiFolks\Statistics\Freq;

$fruits = ['🍈', '🍈', '🍈', '🍉','🍉','🍉','🍉','🍉','🍌'];
$freqTable = Freq::frequencies($fruits);
print_r($freqTable);
```
You can see the frequency table as an array:
```
Array
(
    [🍈] => 3
    [🍉] => 5
    [🍌] => 1
)
```
#### Freq::relativeFrequencies( array $data )
You can retrieve the frequency table in relative format (percentage):
```php
$freqTable = Freq::relativeFrequencies($fruits, 2);
print_r($freqTable);
```
You can see the frequency table as an array with percentage of the occurrences:
```
Array
(
    [🍈] => 33.33
    [🍉] => 55.56
    [🍌] => 11.11
)
```

#### Freq::frequencyTableBySize( array $data , $size)

If you want to create a frequency table based on class (ranges of values) you can use frequencyTableBySize.
The first parameter is the array, and the second one is the size of classes.

Calculate the frequency table with classes. Each group size is 4
```php
$data = [1,1,1,4,4,5,5,5,6,7,8,8,8,9,9,9,9,9,9,10,10,11,12,12,
    13,14,14,15,15,16,16,16,16,17,17,17,18,18, ];
$result = \HiFolks\Statistics\Freq::frequencyTableBySize($data, 4);
print_r($result);
/*
Array
(
    [1] => 5
    [5] => 8
    [9] => 11
    [13] => 9
    [17] => 5
)
 */
```

#### Freq::frequencyTable()

If you want to create a frequency table based on class (ranges of values) you can use frequencyTable.
The first parameter is the array, and the second one is the number of classes.

Calculate the frequency table with 5 classes.
```php
$data = [1,1,1,4,4,5,5,5,6,7,8,8,8,9,9,9,9,9,9,10,10,11,12,12,
    13,14,14,15,15,16,16,16,16,17,17,17,18,18, ];
$result = \HiFolks\Statistics\Freq::frequencyTable($data, 5);
print_r($result);
/*
Array
(
    [1] => 5
    [5] => 8
    [9] => 11
    [13] => 9
    [17] => 5
)
 */
```


### Statistics class

The methods provided by the `Freq` and the `Stat` classes are mainly **static** methods.
If you prefer to use an object instance for calculating statistics you can choose to use an instance of the `Statistics` class.
So for calling the statistics methods, you can use your object instance of the `Statistics` class.

For example for calculating the mean, you can obtain the `Statistics` object via the `make()` static method, and then use the new object `$stat` like in the following example:

```php
$stat = HiFolks\Statistics\Statistics::make(
    [3,5,4,7,5,2]
);
echo $stat->valuesToString(5) . PHP_EOL;
// 2,3,4,5,5
echo "Mean              : " . $stat->mean() . PHP_EOL;
// Mean              : 4.3333333333333
echo "Count             : " . $stat->count() . PHP_EOL;
// Count             : 6
echo "Median            : " . $stat->median() . PHP_EOL;
// Median            : 4.5
echo "First Quartile  : " . $stat->firstQuartile() . PHP_EOL;
// First Quartile  : 2.5
echo "Third Quartile : " . $stat->thirdQuartile() . PHP_EOL;
// Third Quartile : 5
echo "Mode              : " . $stat->mode() . PHP_EOL;
// Mode              : 5
```

#### Calculate Frequency Table

The `Statistics` packages have some methods for generating Frequency Table:
- `frequencies()`: a frequency is the number of times a value of the data occurs;
- `relativeFrequencies()`: a relative frequency is the ratio (fraction or proportion) of the number of times a value of the data occurs in the set of all outcomes to the total number of outcomes;
- `cumulativeFrequencies()`: is the accumulation of the previous relative frequencies;
- `cumulativeRelativeFrequencies()`: is the accumulation of the previous relative ratio.

```php
use HiFolks\Statistics\Statistics;

$s = Statistics::make(
    [98, 90, 70,18,92,92,55,83,45,95,88,76]
);
$a = $s->frequencies();
print_r($a);
/*
Array
(
    [18] => 1
    [45] => 1
    [55] => 1
    [70] => 1
    [76] => 1
    [83] => 1
    [88] => 1
    [90] => 1
    [92] => 2
    [95] => 1
    [98] => 1
)
 */

$a = $s->relativeFrequencies();
print_r($a);
/*
Array
(
    [18] => 8.3333333333333
    [45] => 8.3333333333333
    [55] => 8.3333333333333
    [70] => 8.3333333333333
    [76] => 8.3333333333333
    [83] => 8.3333333333333
    [88] => 8.3333333333333
    [90] => 8.3333333333333
    [92] => 16.666666666667
    [95] => 8.3333333333333
    [98] => 8.3333333333333
)
 */

```
## `NormalDist` class

The `NormalDist` class provides an easy way to work with normal distributions in PHP. It allows you to calculate probabilities and densities for a given mean (μ\muμ) and standard deviation (σ\sigmaσ).

### Key features

- Define a normal distribution with mean (μ\muμ) and standard deviation (σ\sigmaσ).
- Calculate the **Probability Density Function (PDF)** to evaluate the relative likelihood of a value.
- Calculate the **Cumulative Distribution Function (CDF)** to determine the probability of a value or lower.
- Calculate the **Inverse Cumulative Distribution Function (inv_cdf)** to find the value for a given probability.

------

### Class constructor

```php
$normalDist = new NormalDist(float $mu = 0.0, float $sigma = 1.0);
```

- `$mu`: The mean (default = `0.0`).
- `$sigma`: The standard deviation (default = `1.0`).
- Throws an exception if `$sigma` is non-positive.

------

### Methods

#### Properties: mean, sigma, and variance

You can access the distribution parameters via getter methods:

```php
$normalDist = new NormalDist(100, 15);
$normalDist->getMean();             // 100.0
$normalDist->getSigma();            // 15.0
$normalDist->getMedian();           // 100.0 (equals mean for normal dist)
$normalDist->getMode();             // 100.0 (equals mean for normal dist)
$normalDist->getVariance();         // 225.0 (sigma squared)
$normalDist->getVarianceRounded(2); // 225.0
```

From samples:

```php
$normalDist = NormalDist::fromSamples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5]);
$normalDist->getVarianceRounded(5); // 0.25767
```

------

#### Creating a normal distribution instance from sample data

The `fromSamples()` static method creates a normal distribution instance with mu and sigma parameters estimated from the sample data.

Example:

```php
$samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
$normalDist = NormalDist::fromSamples($samples);
$normalDist->getMeanRounded(5); // 2.71667
$normalDist->getSigmaRounded(5); // 0.50761
```

#### Generate random samples `samples($n, $seed)`

Generates `$n` random samples from the normal distribution using the Box-Muller transform. An optional `$seed` parameter allows reproducible results.

```php
$normalDist = new NormalDist(100, 15);

// Generate 5 random samples
$samples = $normalDist->samples(5);
// e.g. [98.3, 112.7, 89.1, 105.4, 101.2]

// Reproducible results with a seed
$samples = $normalDist->samples(1000, seed: 42);
```

------

#### Z-score `zscore($x)`

Computes the standard score describing `$x` in terms of the number of standard deviations above or below the mean: `(x - mu) / sigma`.

```php
$normalDist = new NormalDist(100, 15);
echo $normalDist->zscore(130);          // 2.0 (two std devs above mean)
echo $normalDist->zscore(85);           // -1.0 (one std dev below mean)
echo $normalDist->zscoreRounded(114, 3); // 0.933
```

------

#### Probability Density Function `pdf($x)`

Calculates the **Probability Density Function** at a given value xxx:

```php
$normalDist->pdf(float $x): float
```

- Input: the value `$x` at which to evaluate the PDF.
- Output: the relative likelihood of `$x` in the distribution.

Example:

```php
$normalDist = new NormalDist(10.0, 2.0);
echo $normalDist->pdf(12.0); // Output: 0.12098536225957168
```

------

#### Cumulative Distribution Function `cdf($x)`

Calculates the **Cumulative Distribution Function** at a given value `$x`:

```php
$normalDist->cdf(float $x): float
```
- Input: the value `$x` at which to evaluate the CDF.
- Output: the probability that a random variable `$x` is less than or equal to `$x`.

Example:

```php
$normalDist = new NormalDist(10.0, 2.0);
echo $normalDist->cdf(12.0); // Output: 0.8413447460685429
```

Calculating both, CDF and PDF:

```php
$normalDist = new NormalDist(10.0, 2.0);

// Calculate PDF at x = 12
$pdf = $normalDist->pdf(12.0);
echo "PDF at x = 12: $pdf\n"; // Output: 0.12098536225957168

// Calculate CDF at x = 12
$cdf = $normalDist->cdf(12.0);
echo "CDF at x = 12: $cdf\n"; // Output: 0.8413447460685429
```

------

#### Inverse Cumulative Distribution Function `invCdf($p)`

Computes the **Inverse Cumulative Distribution Function** (also known as the quantile function or percent-point function). Given a probability `$p`, it finds the value `$x` such that `cdf($x) = $p`.

```php
$normalDist->invCdf(float $p): float
```

- Input: a probability `$p` in the range (0, 1) exclusive.
- Output: the value `$x` where `cdf($x) = $p`.
- Throws an exception if `$p` is not in (0, 1).

Example:

```php
$normalDist = new NormalDist(0.0, 1.0);

// Find the value at the 95th percentile of a standard normal distribution
echo $normalDist->invCdfRounded(0.95, 5); // Output: 1.64485

// The median of a standard normal distribution
echo $normalDist->invCdf(0.5); // Output: 0.0
```

The `invCdf()` method is useful for:
- **Confidence intervals**: find critical values for a given confidence level.
- **Hypothesis testing**: determine thresholds for statistical significance.
- **Percentile calculations**: find the value corresponding to a specific percentile.

Round-trip example with `cdf()`:

```php
$normalDist = new NormalDist(100, 15);

// inv_cdf(0.5) equals the mean
echo $normalDist->invCdf(0.5); // Output: 100.0

// Round-trip: cdf(invCdf(p)) ≈ p
echo $normalDist->cdfRounded($normalDist->invCdf(0.25), 2); // Output: 0.25
```

------

#### Quantiles `quantiles($n)`

Divides the normal distribution into `$n` continuous intervals with equal probability. Returns a list of `$n - 1` cut points separating the intervals.
Set `$n` to 4 for quartiles (the default), `$n` to 10 for deciles, or `$n` to 100 for percentiles.

```php
$normalDist = new NormalDist(0.0, 1.0);

// Quartiles (default)
$normalDist->quantiles();    // [-0.6745, 0.0, 0.6745]

// Deciles
$normalDist->quantiles(10);  // 9 cut points

// Percentiles
$normalDist->quantiles(100); // 99 cut points
```

------

#### Overlapping coefficient `overlap($other)`

Computes the overlapping coefficient (OVL) between two normal distributions. Measures the agreement between two normal probability distributions. Returns a value between 0.0 and 1.0 giving the overlapping area in the two underlying probability density functions.

```php
$n1 = new NormalDist(2.4, 1.6);
$n2 = new NormalDist(3.2, 2.0);
echo $n1->overlapRounded($n2, 4); // 0.8035

// Identical distributions overlap completely
$n3 = new NormalDist(0, 1);
echo $n3->overlap($n3); // 1.0
```

------

#### Combining a normal distribution via `add()` method

The `add()` method allows you to combine a NormalDist instance with either a constant or another NormalDist object.
This operation supports mathematical transformations and the combination of distributions.

The use cases are:
- Shifting a distribution: add a constant to shift the mean, useful in translating data.
- Combining distributions: combine independent or jointly normally distributed variables, commonly used in statistics and probability.

```php
$birth_weights = NormalDist::fromSamples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5]);
$drug_effects = new NormalDist(0.4, 0.15);
$combined = $birth_weights->add($drug_effects);

$combined->getMeanRounded(1); // 3.1
$combined->getSigmaRounded(1); // 0.5

$birth_weights->getMeanRounded(5); // 2.71667
$birth_weights->getSigmaRounded(5); // 0.50761
```

#### Scaling a normal distribution by a costant via `multiply()` method

The `multiply()` method for NormalDist multiplies both the mean (mu) and standard deviation (sigma) by a constant.
This method is useful for rescaling distributions, such as when changing measurement units.
The standard deviation is scaled by the absolute value of the constant to ensure it remains non-negative.

The method does not modify the existing object but instead returns a new NormalDist instance with the updated values.

Use Cases:
- Rescaling distributions: useful when changing units (e.g., from meters to kilometers, or Celsius to Farenhait).
- Transforming data: apply proportional scaling to statistical data.

```php
$tempFebruaryCelsius = new NormalDist(5, 2.5); # Celsius
$tempFebFahrenheit = $tempFebruaryCelsius->multiply(9 / 5)->add(32); # Fahrenheit
$tempFebFahrenheit->getMeanRounded(1); // 41.0
$tempFebFahrenheit->getSigmaRounded(1); // 4.5
```


#### Subtracting from a normal distribution via `subtract()` method

The `subtract()` method is the counterpart to `add()`. It subtracts a constant or another NormalDist instance from this distribution.

- A constant (float): shifts the mean down, leaving sigma unchanged.
- A NormalDist instance: subtracts the means and combines the variances.

```php
$nd = new NormalDist(100, 15);
$shifted = $nd->subtract(32);
$shifted->getMean();  // 68.0
$shifted->getSigma(); // 15.0 (unchanged)
```

#### Dividing a normal distribution by a constant via `divide()` method

The `divide()` method is the counterpart to `multiply()`. It divides both the mean (mu) and standard deviation (sigma) by a constant.

```php
// Convert Fahrenheit back to Celsius: (F - 32) / (9/5)
$tempFahrenheit = new NormalDist(41, 4.5);
$tempCelsius = $tempFahrenheit->subtract(32)->divide(9 / 5);
$tempCelsius->getMeanRounded(1);  // 5.0
$tempCelsius->getSigmaRounded(1); // 2.5
```

------

### References for NormalDist

This class is inspired by Python’s `statistics.NormalDist` and aims to provide similar functionality for PHP users. (Work in Progress)



## StreamingStat (Experimental)

> **Note**: `StreamingStat` is experimental in version 1.x. It will be released as stable in version 2. If you want to provide feedback, we are happy to hear from you — please open an issue at https://github.com/Hi-Folks/statistics/issues.

`StreamingStat` computes descriptive statistics in a single pass with O(1) memory, ideal for large datasets or generator-based streams.

```php
use HiFolks\Statistics\StreamingStat;

$s = new StreamingStat();
$s->add(1)->add(2)->add(3)->add(4)->add(5);

$s->count();     // 5
$s->sum();       // 15.0
$s->min();       // 1.0
$s->max();       // 5.0
$s->mean();      // 3.0
$s->variance();  // 2.5
$s->stdev();     // 1.5811...
$s->skewness();  // 0.0
$s->kurtosis();  // -1.2
```

| Method | Description | Min n |
|---|---|---|
| `count()` | Number of values added | 0 |
| `sum()` | Sum of all values | 1 |
| `min()` | Minimum value | 1 |
| `max()` | Maximum value | 1 |
| `mean(?int $round = null)` | Arithmetic mean | 1 |
| `variance(?int $round = null)` | Sample variance | 2 |
| `pvariance(?int $round = null)` | Population variance | 1 |
| `stdev(?int $round = null)` | Sample standard deviation | 2 |
| `pstdev(?int $round = null)` | Population standard deviation | 1 |
| `skewness(?int $round = null)` | Sample skewness (adjusted Fisher-Pearson) | 3 |
| `pskewness(?int $round = null)` | Population skewness | 3 |
| `kurtosis(?int $round = null)` | Excess kurtosis (sample) | 4 |

All methods throw `InvalidDataInputException` when insufficient data is available.

## Testing

```bash
composer run test           Runs the test script
composer run test-coverage  Runs the test-coverage script
composer run format         Runs the format script
composer run static-code    Runs the static-code script
composer run all-check      Runs the all-check script
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Roberto B.](https://github.com/roberto-butti)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
