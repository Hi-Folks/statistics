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

```php
$stat = HiFolks\Statistics\Statistics::make(
    [3,5,4,7,5,2]
);
echo $stat->valuesToString(5) . PHP_EOL;
// 2,3,4,5,5
echo "Mean              : " . $stat->getMean() . PHP_EOL;
// Mean              : 4.3333333333333
echo "Count             : " . $stat->getCount() . PHP_EOL;
// Count             : 6
echo "Median            : " . $stat->getMedian() . PHP_EOL;
// Median            : 4.5
echo "Lower Percentile  : " . $stat->getLowerPercentile() . PHP_EOL;
// Lower Percentile  : 2.5
echo "Higher Percentile : " . $stat->getHigherPercentile() . PHP_EOL;
// Higher Percentile : 5
echo "Mode              : " . $stat->getMode() . PHP_EOL;
// Mode              : 5
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
