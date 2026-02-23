# Changelog

## 1.3.1 - 2026-02-23
- Adding `tTestTwoSample()` method for two-sample independent t-test (Welch's t-test) — compares the means of two independent groups without assuming equal variances
- Adding `tTestPaired()` method for paired t-test — tests whether the mean difference between paired observations (e.g. before/after) is significantly different from zero
- Adding `StudentT` class for the Student's t-distribution (pdf, cdf, invCdf) — building block for t-tests and confidence intervals with small samples
- Adding `tTest()` method for one-sample t-test — like z-test but appropriate for small samples where the population standard deviation is unknown
- Adding `zTest()` method for one-sample Z-test — tests whether the sample mean differs significantly from a hypothesized population mean (includes p-value calculation)
- Adding `Alternative` enum (`TwoSided`, `Greater`, `Less`) for hypothesis testing
- Adding `confidenceInterval()` method for computing confidence intervals for the mean using the normal (z) distribution
- Adding `rSquared()` method for R² (coefficient of determination) — proportion of variance explained by linear regression

## 1.3.0 - 2026-02-22
- Adding `StreamingStat` class (experimental) for streaming/online computation of mean, variance, stdev, skewness, kurtosis, sum, min, and max with O(1) memory
- Adding `percentile()` method for computing the value at any percentile (0–100) with linear interpolation
- Adding `coefficientOfVariation()` method for relative dispersion (CV%), supporting both sample and population modes
- Adding `trimmedMean()` method for robust central tendency — computes the mean after removing outliers from each side
- Adding `weightedMedian()` method for computing the median with weighted observations
- Adding `sem()` method for standard error of the mean
- Adding `meanAbsoluteDeviation()` method for mean absolute deviation — average distance from the mean
- Adding `medianAbsoluteDeviation()` method for median absolute deviation — robust dispersion measure resistant to outliers
- Adding `zscores()` method for computing z-scores of each value in a dataset
- Adding `outliers()` method for z-score based outlier detection with configurable threshold
- Adding `iqrOutliers()` method for IQR-based outlier detection (box plot whiskers), robust for skewed data
- Adding `rSquared()` method for R² (coefficient of determination) — proportion of variance explained by linear regression

## 1.2.5 - 2026-02-22
- Adding `kurtosis()` method for excess kurtosis

## 1.2.4 - 2026-02-21
- Adding `skewness()` method for adjusted Fisher-Pearson sample skewness
- Adding `pskewness()` method for population (biased) skewness
- Full Coverage Tests (adding some edge cases)
- Create KDE example

## 1.2.3 - 2026-02-21
- Adding `kde()` method for Kernel Density Estimation — returns a closure that estimates PDF or CDF from sample data, supporting 9 kernel functions with aliases
- Adding `kdeRandom()` method for random sampling from a Kernel Density Estimate — returns a closure that generates random floats from the KDE distribution
- Introducing `KdeKernel` backed string enum — `kde()` and `kdeRandom()`. It accepts `KdeKernel` enum cases
- Adding Kernel Density Estimation (KDE) examples

## 1.2.2 - 2026-02-21
- Adding `method` parameter to `quantiles()` supporting `'exclusive'` (default) and `'inclusive'` interpolation methods
- Adding `medianGrouped()` method for estimating the median of grouped/binned continuous data using interpolation
- Adding Spearman rank correlation via `method` parameter in `correlation()` (`method='ranked'`)
- Adding proportional linear regression via `proportional` parameter in `linearRegression()` for regression through the origin
- Adding optional pre-computed mean parameter to `variance()` (`xbar`) and `pvariance()` (`mu`)


## 1.2.1 - 2026-02-20
- Adding `invCdf()` method to normal distribution
- Adding `getVariance()` method to normal distribution (sigma squared)
- Adding `getMedian()` method to normal distribution (equals mean)
- Adding `getMode()` method to normal distribution (equals mean)
- Adding `quantiles()` method to normal distribution (divide into n equal-probability intervals)
- Adding `overlap()` method to normal distribution (overlapping coefficient between two distributions)
- Adding `zscore()` method to normal distribution (standard score)
- Adding `samples()` method to normal distribution (generate random samples with optional seed)
- Adding `subtract()` method to normal distribution (counterpart to add)
- Adding `divide()` method to normal distribution (counterpart to multiply)

## 1.2.0 - 2026-02-19
- Welcome to PHP 8.5
- Upgrading to PHPstan new rules (offsetAccess)
- Tests migrated from PestPHP 2 to PHPUnit 11
- Code Syntax checker from Pint to PHP CS Fixer

## 1.1.4 - 2025-04-25
- Adding `fmean()` method for computing the arithmetic mean with float numbers.

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
