  Missing Functions

  Python Function: kde(data, h, kernel)
  Description: Kernel Density Estimation
  Status: Missing
  ────────────────────────────────────────
  Python Function: kde_random(data, h, kernel)
  Description: Random sampling from KDE
  Status: Missing





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
