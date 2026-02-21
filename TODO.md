  Missing Functions


  Python Function: kde(data, h, kernel)
  Description: Kernel Density Estimation
  Status: Missing
  ────────────────────────────────────────
  Python Function: kde_random(data, h, kernel)
  Description: Random sampling from KDE
  Status: Missing

  Missing Parameters/Variants


  Feature: linear_regression() with proportional=True
  Python: Supports proportional regression (intercept forced to 0)
  This Package: No proportional option
  ────────────────────────────────────────
  Feature: variance(data, xbar) / pvariance(data, mu)
  Python: Can pass pre-computed mean to avoid recalculation
  This Package: No pre-computed mean parameter
  ────────────────────────────────────────
  Feature: quantiles() with method='inclusive'
  Python: Supports both exclusive and inclusive methods
  This Package: No method parameter

  Summary

  The package is actually very close to full parity with Python's statistics
  module. The gaps are:

  1. median_grouped - interpolation-based median for grouped/binned data
  2. kde / kde_random - Kernel Density Estimation (added in Python 3.13,
  relatively new)
  3. Spearman rank correlation - via method parameter on correlation()
  4. Proportional linear regression - forcing intercept through origin
  5. Minor parameter additions (xbar/mu on variance/stdev, method on quantiles)

  Items 1, 3, and 4 would be the most practical additions to reach near-complete
   parity with Python's statistics module. The KDE functions (2) are newer and
  more niche.




  Currently Implemented (for reference)

  Central tendency, variance/stdev, median variants, mode/multimode,
  geometric/harmonic mean, quantiles, covariance, correlation, linear
  regression, normal distribution (PDF, CDF, inverse CDF, z-score), frequency
  tables.

  ---
  Missing Functions

  Descriptive Statistics

  - Trimmed/Truncated mean - mean after removing outliers (top/bottom x%)
  - Weighted median - median with weights (like fmean supports weights, but
  median doesn't)
  - Skewness - measure of asymmetry of the distribution
  - Kurtosis - measure of "tailedness" of the distribution
  - Standard error of the mean (SEM)
  - Coefficient of variation (CV) - stdev / mean, useful for comparing
  variability across datasets
  - Mean absolute deviation (MAD)
  - Percentile - arbitrary percentile (e.g., 90th percentile) — quantiles()
  exists but a direct percentile($data, $p) would be convenient

  Correlation & Regression

  - Spearman rank correlation - non-parametric correlation
  - Kendall tau correlation - another rank-based correlation
  - Multiple/polynomial regression
  - R-squared (coefficient of determination)

  Hypothesis Testing

  - T-test (one-sample, two-sample, paired)
  - Chi-squared test
  - Z-test
  - P-value calculation
  - Confidence intervals

  Other Distributions (beyond Normal)

  - Student's t-distribution
  - Chi-squared distribution
  - Binomial distribution
  - Poisson distribution
  - Uniform distribution
  - Exponential distribution

  Outlier Detection

  - IQR-based outlier detection (the building blocks exist with
  firstQuartile/thirdQuartile, but no dedicated method)
  - Z-score based outlier detection

  Ranking & Order Statistics

  - Rank - assign ranks to data points
  - Percentile rank - what percentile a given value falls at

  ---
  The most impactful additions would likely be skewness, kurtosis, coefficient
  of variation, percentile, and Spearman correlation — these are commonly needed
   and align well with the package's existing scope (inspired by Python's
  statistics module).
