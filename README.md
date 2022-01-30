![PHP package for Statistics](https://repository-images.githubusercontent.com/445609326/e2539776-0f8f-4556-be1d-887ea2368813)

# Statistics PHP package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hi-folks/statistics.svg?style=flat-square)](https://packagist.org/packages/hi-folks/statistics)
[![Tests](https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/hi-folks/statistics.svg?style=flat-square)](https://packagist.org/packages/hi-folks/statistics)


PHP package that provides functions for calculating mathematical statistics of numeric data.

In this package I'm collecting some useful statistic functions.
Once upon a time, I was playing with FIT files. A FIT file is a file where is collected a lot of information about your sport activities. In that file you have the tracking of your Hearth Rate, Speed, Cadence, Power etc.
I needed to apply some statistic functions to understand better the numbers and the sport activity performance. I collected some functions like:
- mean: the average of the data set (and geometric mean);
- mode: the most common number in data set (and multi mode);
- median: the middle of the set of values (median low and median high);
- range: the difference between the largest and smallest values
- first quartile ( or lowest percentile);
- third quartile (or highest percentile);
- frequency table (cumulative, relative);
- standard deviation (population and sample);
- variance (population and sample);
- etc...

> This package is inspired by the [Python statistics module](https://docs.python.org/3/library/statistics.html) 

## Installation

You can install the package via composer:

```bash
composer require hi-folks/statistics
```

## Usage

### Stat class
This class provides methods for calculating mathematical statistics of numeric data.
Stat class has methods to calculate an average or typical value from a population or sample like:
- mean(): arithmetic mean or "average" of data;
- median(): median or "middle value" of data;
- medianLow(): low median of data;
- medianHigh(): high median of data;
- mode(): single mode (most common value) of discrete or nominal data;
- multimode(): list of modes (most common values) of discrete or nominal data;
- higherPercentile(): 3rd quartile, is the value at which 75 percent of the data is below it;
- lowerPercentile(): first quartile, is the value at which 25 percent of the data is below it;
- pstdev(): Population standard deviation
- stdev(): Sample standard deviation
- pvariance(): variance for a population
- variance(): variance for a sample
- geometricMean(): geometric mean
- harmonicMean(): harmonic mean

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
The harmonic mean is the reciprocal of the arithmetic mean() of the reciprocals of the data. For example, the harmonic mean of three values a, b and c will be equivalent to 3/(1/a + 1/b + 1/c). If one of the values is zero, the result will be zero.

```php
use HiFolks\Statistics\Stat;
$mean = Stat::harmonicMean([40, 60], null, 1);
// 48.0
```

You can also calculate harmonic weighted mean.
Suppose a car travels 40 km/hr for 5 km, and when traffic clears, speeds-up to 60 km/hr for the remaining 30 km of the journey. What is the average speed?

```php
use HiFolks\Statistics\Stat;
Stat::harmonicMean([40, 60], [5, 30], 1);
// 56.0
```
where:
- 40, 60 :  are the elements
- 5, 30: are the weights for each element (first weight is the weight of the first element, the second one is the weight of the second element)
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

For calculate variance from a *sample*:
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

### Freq class
With *Statistics* package you can calculate frequency table.
A frequency table is list the frequency of various outcomes in a sample.
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

### Statistics class

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
echo "Lower Percentile  : " . $stat->lowerPercentile() . PHP_EOL;
// Lower Percentile  : 2.5
echo "Higher Percentile : " . $stat->higherPercentile() . PHP_EOL;
// Higher Percentile : 5
echo "Mode              : " . $stat->mode() . PHP_EOL;
// Mode              : 5
```

### Calculate Frequency Table

Statistics packages has some methods for generating Frequency Table:
- frequencies(): a frequency is the number of times a value of the data occurs;
- relativeFrequencies(): a relative frequency is the ratio (fraction or proportion) of the number of times a value of the data occurs in the set of all outcomes to the total number of outcomes;
- cumulativeFrequencies(): is the accumulation of the previous relative frequencies;
- cumulativeRelativeFrequencies(): is the accumulation of the previous relative ratio.

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
