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
| `median()` | median or "middle value" of data |
| `medianLow()` | low median of data |
| `medianHigh()` | high median of data |
| `mode()` | single mode (most common value) of discrete or nominal data |
| `multimode()` | list of modes (most common values) of discrete or nominal data |
| `quantiles()` | cut points dividing the range of a probability distribution into continuous intervals with equal probabilities |
| `thirdQuartile()` | 3rd quartile, is the value at which 75 percent of the data is below it |
| `firstQuartile()` | first quartile, is the value at which 25 percent of the data is below it |
| `pstdev()` | Population standard deviation |
| `stdev()` | Sample standard deviation |
| `pvariance()` | variance for a population |
| `variance()` | variance for a sample |
| `geometricMean()` | geometric mean |
| `harmonicMean()` | harmonic mean |
| `correlation()` | the Pearson’s correlation coefficient for two inputs |
| `covariance()` | the sample covariance of two inputs |
| `linearRegression()` | return the slope and intercept of simple linear regression parameters estimated using ordinary least squares |

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

#### Stat::quantiles( array $data, $n=4, $round=null  )
Divide data into n continuous intervals with equal probability. Returns a list of n - 1 cut points separating the intervals.
Set n to 4 for quartiles (the default). Set n to 10 for deciles. Set n to 100 for percentiles which gives the 99 cut points that separate data into 100 equal-sized groups.


```php
use HiFolks\Statistics\Stat;
$quantiles = Stat::quantiles([98, 90, 70,18,92,92,55,83,45,95,88]);
// [ 55.0, 88.0, 92.0 ]
$quantiles = Stat::quantiles([105, 129, 87, 86, 111, 111, 89, 81, 108, 92, 110,100, 75, 105, 103, 109, 76, 119, 99, 91, 103, 129,106, 101, 84, 111, 74, 87, 86, 103, 103, 106, 86,111, 75, 87, 102, 121, 111, 88, 89, 101, 106, 95,103, 107, 101, 81, 109, 104], 10);
// [81.0, 86.2, 89.0, 99.4, 102.5, 103.6, 106.0, 109.8, 111.0]
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

#### Stat::variance ( array $data)
Variance is a measure of dispersion of data points from the mean.
Low variance indicates that data points are generally similar and do not vary widely from the mean.
High variance indicates that data values have greater variability and are more widely dispersed from the mean.

To calculate the variance from a *sample*:
```php
use HiFolks\Statistics\Stat;
$variance = Stat::variance([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5]);
// 1.3720238095238095
```

If you need to calculate the variance on the whole population and not just on a sample you need to use *pvariance* method:
```php
use HiFolks\Statistics\Stat;
$variance = Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25]);
// 1.25
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

#### Stat::correlation ( array $x , array $y )
Return the Pearson’s correlation coefficient for two inputs. Pearson’s correlation coefficient r takes values between -1 and +1. It measures the strength and direction of the linear relationship, where +1 means very strong, positive linear relationship, -1 very strong, negative linear relationship, and 0 no linear relationship.

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

#### Stat::linearRegression ( array $x , array $y )
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

#### 1. `pdf($x)`

Calculates the **Probability Density Function** at a given value xxx:

```php
$normalDist->pdf(float $x): float
```

**Input**: The value `$x` at which to evaluate the PDF.
**Output**: The relative likelihood of `$x` in the distribution.

Example:

```php
$normalDist = new NormalDist(10.0, 2.0);
echo $normalDist->pdf(12.0); // Output: 0.12098536225957168
```

------

#### 2. `cdf($x)`

Calculates the **Cumulative Distribution Function** at a given value `$x`:

```php
$normalDist->cdf(float $x): float
```
**Input**: The value `$x` at which to evaluate the CDF.
**Output**: The probability that a random variable `$x` is less than or equal to `$x`.
Example:

```php
$normalDist = new NormalDist(10.0, 2.0);
echo $normalDist->cdf(12.0); // Output: 0.8413447460685429
```

------

### Use case example

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

### References for NormalDist

This class is inspired by Python’s `statistics.NormalDist` and provides similar functionality for PHP users.



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
