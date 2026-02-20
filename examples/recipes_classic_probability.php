<?php

require __DIR__ . '/../vendor/autoload.php';

use HiFolks\Statistics\NormalDist;

/**
 * Recipe: Classic Probability Problems
 *
 * Adapted from the Python statistics module "Examples and Recipes":
 * https://docs.python.org/3/library/statistics.html#examples-and-recipes
 *
 * Using NormalDist to solve classic probability problems.
 */

echo "=== Classic Probability Problems ===" . PHP_EOL . PHP_EOL;

// --- SAT scores are normally distributed with mean 1060 and std dev 195 ---
$sat = new NormalDist(1060, 195);

// What percentage of students score between 1100 and 1200?
// Adding 0.5 applies a continuity correction for discrete scores.
$fraction = $sat->cdf(1200 + 0.5) - $sat->cdf(1100 - 0.5);
echo "Percentage of students scoring between 1100 and 1200: "
    . round($fraction * 100, 1) . "%" . PHP_EOL;

// Quartiles: divide SAT scores into 4 equal-probability groups
echo PHP_EOL . "--- SAT Score Quartiles ---" . PHP_EOL;
$quartiles = $sat->quantiles(4);
echo "Quartiles (Q1, Q2, Q3): "
    . implode(', ', array_map(round(...), $quartiles))
    . PHP_EOL;

// Deciles: divide SAT scores into 10 equal-probability groups
echo PHP_EOL . "--- SAT Score Deciles ---" . PHP_EOL;
$deciles = $sat->quantiles(10);
echo "Deciles: "
    . implode(', ', array_map(round(...), $deciles))
    . PHP_EOL;

// --- What SAT score is needed to be in the top 10%? ---
echo PHP_EOL . "--- SAT Score Thresholds ---" . PHP_EOL;
$top10 = $sat->invCdfRounded(0.90, 0);
echo "SAT score needed for top 10%: " . $top10 . PHP_EOL;

$top1 = $sat->invCdfRounded(0.99, 0);
echo "SAT score needed for top 1%: " . $top1 . PHP_EOL;

// --- Probability of scoring above a threshold ---
$threshold = 1300;
$probAbove = 1 - $sat->cdf($threshold);
echo PHP_EOL . "Probability of scoring above " . $threshold . ": "
    . round($probAbove * 100, 1) . "%" . PHP_EOL;

// --- Z-score for a specific SAT score ---
$score = 1250;
$z = $sat->zscoreRounded($score, 2);
echo "Z-score for SAT score of " . $score . ": " . $z . PHP_EOL;
