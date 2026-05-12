## Missing Functions

### Priority 1: Ranking & Order Statistics

- DONE: `rank()` - assign ranks to data points.
  - Supports tie strategies: `average`, `min`, `max`, `dense`, `ordinal`.
- DONE: `percentileRank()` - calculate what percentile a given value falls at.
  - Supports `weak`, `strict`, `mean`, and `rank` variants.

### Priority 2: Correlation

- DONE: `kendallTau()` - Kendall tau-b rank correlation.
  - Useful for ordinal data, small samples, and datasets with ties.
- DONE: Extend `correlation()` with a Kendall method option.

### Priority 3: Hypothesis Testing

- ~~T-test (two-sample, paired) - one-sample is done~~ DONE: `tTestTwoSample()` (Welch's) and `tTestPaired()`.
- DONE: `chiSquaredTest()` - chi-squared goodness-of-fit test.
- DONE: `chiSquaredIndependence()` - chi-squared test for contingency tables.

### Priority 4: Distributions

- `ChiSquaredDist`
  - Needed for chi-squared tests.
  - Include `pdf()`, `cdf()`, `invCdf()` if practical, mean, variance.
- `BinomialDist`
  - Include `pmf()`, `cdf()`, mean, variance, samples.
- `PoissonDist`
  - Include `pmf()`, `cdf()`, mean, variance, samples.
- `ExponentialDist`
  - Include `pdf()`, `cdf()`, `invCdf()`, mean, variance, samples.
- `UniformDist`
  - Include `pdf()`, `cdf()`, `invCdf()`, mean, variance, samples.

### Priority 5: Statistics Wrapper Completeness

Add fluent `Statistics` wrapper methods for existing `Stat` APIs where useful:

- `correlation()`
- `covariance()`
- `linearRegression()`
- `logarithmicRegression()`
- `powerRegression()`
- `exponentialRegression()`
- `rSquared()`
- `kde()`
- `kdeRandom()`

### Priority 6: Regression & Modeling

- `polynomialRegression()` - fit polynomial models of configurable degree.
- `multipleLinearRegression()` - fit linear models with multiple predictors.
  - This likely needs a small matrix/linear-algebra helper layer.
  - Add after simpler ranking, correlation, testing, and distribution work.
