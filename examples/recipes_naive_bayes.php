<?php

require __DIR__ . '/../vendor/autoload.php';

use HiFolks\Statistics\NormalDist;

/**
 * Recipe: Naive Bayesian Classifier
 *
 * Adapted from the Python statistics module "Examples and Recipes":
 * https://docs.python.org/3/library/statistics.html#examples-and-recipes
 *
 * A simple Naive Bayes classifier using NormalDist.
 * Given training data for height, weight, and foot size of males
 * and females, classify a new person based on their measurements.
 */
echo "=== Naive Bayesian Classifier ===" . PHP_EOL . PHP_EOL;

// --- Training data ---
// Fit normal distributions to each feature for each class

echo "--- Training Phase ---" . PHP_EOL;

$heightMale = NormalDist::fromSamples([6, 5.92, 5.58, 5.92]);
$heightFemale = NormalDist::fromSamples([5, 5.5, 5.42, 5.75]);

$weightMale = NormalDist::fromSamples([180, 190, 170, 165]);
$weightFemale = NormalDist::fromSamples([100, 150, 130, 150]);

$footSizeMale = NormalDist::fromSamples([12, 11, 12, 10]);
$footSizeFemale = NormalDist::fromSamples([6, 8, 7, 9]);

echo "Height (male):    mu=" . $heightMale->getMeanRounded(2)
    . ", sigma=" . $heightMale->getSigmaRounded(2) . PHP_EOL;
echo "Height (female):  mu=" . $heightFemale->getMeanRounded(2)
    . ", sigma=" . $heightFemale->getSigmaRounded(2) . PHP_EOL;
echo "Weight (male):    mu=" . $weightMale->getMeanRounded(2)
    . ", sigma=" . $weightMale->getSigmaRounded(2) . PHP_EOL;
echo "Weight (female):  mu=" . $weightFemale->getMeanRounded(2)
    . ", sigma=" . $weightFemale->getSigmaRounded(2) . PHP_EOL;
echo "Foot size (male): mu=" . $footSizeMale->getMeanRounded(2)
    . ", sigma=" . $footSizeMale->getSigmaRounded(2) . PHP_EOL;
echo "Foot size (female): mu=" . $footSizeFemale->getMeanRounded(2)
    . ", sigma=" . $footSizeFemale->getSigmaRounded(2) . PHP_EOL;

// --- Classification ---
echo PHP_EOL . "--- Classification Phase ---" . PHP_EOL . PHP_EOL;

// Person to classify
$ht = 6.0;    // height in feet
$wt = 130;    // weight in pounds
$fs = 8;      // foot size

echo "New person: height=" . $ht . "ft, weight=" . $wt
    . "lbs, foot size=" . $fs . PHP_EOL . PHP_EOL;

// Equal prior probabilities
$priorMale = 0.5;
$priorFemale = 0.5;

// Posterior ∝ prior × P(height|class) × P(weight|class) × P(foot_size|class)
// Naive Bayes assumes features are conditionally independent.
$posteriorMale = $priorMale
    * $heightMale->pdf($ht)
    * $weightMale->pdf($wt)
    * $footSizeMale->pdf($fs);

$posteriorFemale = $priorFemale
    * $heightFemale->pdf($ht)
    * $weightFemale->pdf($wt)
    * $footSizeFemale->pdf($fs);

echo "Posterior (male):   " . sprintf("%.4e", $posteriorMale) . PHP_EOL;
echo "Posterior (female): " . sprintf("%.4e", $posteriorFemale) . PHP_EOL;
echo PHP_EOL;

$classification = $posteriorMale > $posteriorFemale ? 'male' : 'female';
echo "Classification: " . $classification . PHP_EOL;

// Show confidence as normalized probability
$total = $posteriorMale + $posteriorFemale;
$confidenceMale = $posteriorMale / $total;
$confidenceFemale = $posteriorFemale / $total;
echo "Confidence (male):   " . round($confidenceMale * 100, 1) . "%" . PHP_EOL;
echo "Confidence (female): " . round($confidenceFemale * 100, 1) . "%" . PHP_EOL;

// --- Classify a second person ---
echo PHP_EOL . "--- Classify another person ---" . PHP_EOL . PHP_EOL;

$ht2 = 5.5;
$wt2 = 175;
$fs2 = 11;

echo "New person: height=" . $ht2 . "ft, weight=" . $wt2
    . "lbs, foot size=" . $fs2 . PHP_EOL . PHP_EOL;

$posteriorMale2 = $priorMale
    * $heightMale->pdf($ht2)
    * $weightMale->pdf($wt2)
    * $footSizeMale->pdf($fs2);

$posteriorFemale2 = $priorFemale
    * $heightFemale->pdf($ht2)
    * $weightFemale->pdf($wt2)
    * $footSizeFemale->pdf($fs2);

$classification2 = $posteriorMale2 > $posteriorFemale2 ? 'male' : 'female';
$total2 = $posteriorMale2 + $posteriorFemale2;

echo "Posterior (male):   " . sprintf("%.4e", $posteriorMale2) . PHP_EOL;
echo "Posterior (female): " . sprintf("%.4e", $posteriorFemale2) . PHP_EOL;
echo "Classification: " . $classification2 . PHP_EOL;
echo "Confidence: " . round(max($posteriorMale2, $posteriorFemale2) / $total2 * 100, 1)
    . "%" . PHP_EOL;
