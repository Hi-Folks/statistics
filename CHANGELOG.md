# Changelog

## 1.1.3 - 2024-12-14
- Adding `multiply()` method to scale NormalDist by a constant

## 1.1.2 - 2024-12-14
- Implementing `add()` method for NormalDist

## 1.1.1 - 2024-12-13
- Implementing fromSample method for NormalDist

## 1.1.0 - 2024-12-13
- Upgrading RectorPHP v 2
- Upgrading PHPStan v 2

## 1.0.2 - 2024-12-10
- NormalDist class, with `cdf()` and `pdf()`
- Fix deprecations for PHP 8.4

## 1.0.1 - 2024-11-21

- Welcome PHP 8.4
- Upgrading to Rector 1

## 1.0.0 - 2023-12-26

- Fixed `median()` function to handle unsorted data by @keatis
- Rector refactor
- PHPstan level 8
- Support for PHP 8.1 and above
- Add support for PHP 8.2 by @AmooAti
- Update to PestPHP v2 by @AmooAti
- Improving documentation (readme, contributing, code of conduct, security policies) by @AbhineshJha, @Arcturus22, @tvermaashutosh, @Abhishekgupta204, @Aryan4884
- Rector v0.18.5 by @sukuasoft
- Introducing Pint by @sukuasoft
- GitHub Actions: Updating actions/checkout v4


## 0.2.1 - 2022-02-22
- Linear regression

## 0.2.0 - 2022-02-21
- Raise Exception instead of returning null if there is no valid input. By Artem Trokhymchuk @trokhymchuk [thanks for the PR #15](https://github.com/Hi-Folks/statistics/pull/15);
- PHPStan, level 9

## 0.1.7 - 2022-02-19
- Code refactoring by @trokhymchuk
- Clean phpdoc blocks by @trokhymchuk
- Stat::correlation()
- PHPStan, level 8

## 0.1.6 -2022-02-17
- Stat::covariance()

## 0.1.5 - 2022-02-05
- frequencyTable()
- frequencyTableBySize()
- code refactoring and documenting some functions by Artem Trokhymchuk @trokhymchuk [thanks for the PR #2](https://github.com/Hi-Folks/statistics/pull/2)
- add tests for Math class

## 0.1.4 - 2022-01-30
- quantiles()
- firstQuartile()
- thirdQuartile()

## 0.1.3 - 2022-01-29
- geometricMean(): geometric mean
- harmonicMean(): harmonic mean and weighted harmonic mean


## 0.1.2 - 2022-01-28

- pstdev(): Population standard deviation
- stdev(): Sample standard deviation
- pvariance(): variance for a population
- variance(): variance for a sample

## 0.1.1 - 2022-01-27

- Create Freq class with static method for managing frequencies table
- Create Stat class with static methods for basci statistic functions like: mean, mode, median, multimode...
- Refactor Statistics class in order to use logic provided by Freq and Stat class
- Create ArrUtil with some helpers/functions to manage arrays
- Add CICD test for PHP 8.1

## Initial release - 2022-01-08

Initial release with:

- getMean()
- count()
- median()
- firstQuartile()
- thirdQuartile()
- mode()
- frequencies(): a frequency is the number of times a value of the data occurs;
- relativeFrequencies(): a relative frequency is the ratio (fraction or proportion) of the number of times a value of the data occurs in the set of all outcomes to the total number of outcomes;
- cumulativeFrequencies(): is the accumulation of the previous relative frequencies.;
- cumulativeRelativeFrequencies(): is the accumulation of the previous relative ratio.

## 0.1.0 - 2022-01-08

Initial release with:

- getMean()
- count()
- median()
- firstQuartile()
- thirdQuartile()
- mode()
- frequencies(): a frequency is the number of times a value of the data occurs;
- relativeFrequencies(): a relative frequency is the ratio (fraction or proportion) of the number of times a value of the data occurs in the set of all outcomes to the total number of outcomes;
- cumulativeFrequencies(): is the accumulation of the previous relative frequencies.;
- cumulativeRelativeFrequencies(): is the accumulation of the previous relative ratio.
