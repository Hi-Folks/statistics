<?php

require __DIR__ . '/../vendor/autoload.php';

use HiFolks\Statistics\NormalDist;
use HiFolks\Statistics\Stat;

/**
 * Recipe: Monte Carlo Inputs for Simulations
 *
 * Adapted from the Python statistics module "Examples and Recipes":
 * https://docs.python.org/3/library/statistics.html#examples-and-recipes
 *
 * NormalDist can generate random samples to use as inputs for
 * Monte Carlo simulations.
 */

echo "=== Monte Carlo Simulation ===" . PHP_EOL . PHP_EOL;

/**
 * A simple model function that combines three uncertain variables.
 */
function model(float $x, float $y, float $z): float
{
    return (3 * $x + 7 * $x * $y - 5 * $y) / (11 * $z);
}

$n = 100_000;

// Generate random samples from three independent normal distributions
$X = (new NormalDist(10, 2.5))->samples($n, seed: 3652260728);
$Y = (new NormalDist(15, 1.75))->samples($n, seed: 4582495471);
$Z = (new NormalDist(50, 1.25))->samples($n, seed: 6582483453);

// Compute the model output for each set of inputs
$results = [];
for ($i = 0; $i < $n; $i++) {
    $results[] = model($X[$i], $Y[$i], $Z[$i]);
}

// Find the quartiles of the model output distribution
$quantiles = Stat::quantiles($results);
echo "Model output quartiles (Q1, Q2, Q3):" . PHP_EOL;
echo "  Q1: " . round($quantiles[0], 4) . PHP_EOL;
echo "  Q2: " . round($quantiles[1], 4) . PHP_EOL;
echo "  Q3: " . round($quantiles[2], 4) . PHP_EOL;

// Basic descriptive statistics of the simulation
echo PHP_EOL . "--- Simulation Summary ---" . PHP_EOL;
echo "Mean:   " . round(Stat::mean($results), 4) . PHP_EOL;
echo "Stdev:  " . round(Stat::stdev($results), 4) . PHP_EOL;
echo "Min:    " . round(min($results), 4) . PHP_EOL;
echo "Max:    " . round(max($results), 4) . PHP_EOL;

// Fit a normal distribution to the simulation results
$fitted = NormalDist::fromSamples($results);
echo PHP_EOL . "--- Fitted Normal Distribution ---" . PHP_EOL;
echo "Estimated mu:    " . $fitted->getMeanRounded(4) . PHP_EOL;
echo "Estimated sigma: " . $fitted->getSigmaRounded(4) . PHP_EOL;

// Use the fitted distribution to answer probability questions
$threshold = 2.0;
$probAbove = 1 - $fitted->cdf($threshold);
echo PHP_EOL . "P(result > " . $threshold . "): "
    . round($probAbove * 100, 1) . "%" . PHP_EOL;
