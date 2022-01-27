# Statistics PHP package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hi-folks/statistics.svg?style=flat-square)](https://packagist.org/packages/hi-folks/statistics)
[![Tests](https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/hi-folks/statistics/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/hi-folks/statistics.svg?style=flat-square)](https://packagist.org/packages/hi-folks/statistics)

PHP package that provides functions for calculating mathematical statistics of numeric data.

In this package I'm collecting some useful statistic functions.
Once upon a time, I was playing with FIT files. A FIT file is a file where is collected a lot of information about your sport activities. In that file you have the tracking of your Hearth Rate, Speed, Cadence, Power etc.
I needed to apply some statistic functions to understand better the numbers and the sport activity performance. I collected some functions like:
- mean: the average of the data set;
- mode: the most common number in data set;
- median: the middle of the set of values;
- range: the difference between the largest and smallest values
- lowest percentile;
- highest percentile;
- frequency table;
- etc...




## Installation

You can install the package via composer:

```bash
composer require hi-folks/statistics
```

## Usage

### Frequencies
With Statistics package you can calculate frequencies table.
A frequencies table is ...


```php
use HiFolks\Statistics\Freq;


$fruits = ['🍈', '🍈', '🍈', '🍉','🍉','🍉','🍉','🍉','🍌'];
$freqTable = Freq::frequencies($fruits);
print_r($freqTable);
```
You can see the frequencies table as an array:
```
Array
(
    [🍈] => 3
    [🍉] => 5
    [🍌] => 1
)
```

You can retrieve the frequencies table in relative format (percentage):
```php
$freqTable = Freq::relativeFrequencies($fruits, 2);
print_r($freqTable);
```
You can see the frequencies table as an array with percentage of the occurrences:
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

### Calculate Frequencies Table

Statistics packages has some methods for generating Frequencies Table:
- frequencies(): a frequency is the number of times a value of the data occurs;
- relativeFrequencies(): a relative frequency is the ratio (fraction or proportion) of the number of times a value of the data occurs in the set of all outcomes to the total number of outcomes;
- cumulativeFrequencies(): is the accumulation of the previous relative frequencies.;
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
composer test
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
