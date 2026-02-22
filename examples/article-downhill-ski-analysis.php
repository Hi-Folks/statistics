<?php

/**
 * Exploring Olympic Downhill Results with PHP Statistics
 *
 * This script accompanies the article:
 * https://dev.to/robertobutti/exploring-olympic-downhill-results-with-php-statistics-3eo1
 *
 * Each section below corresponds to a step in the article.
 * Run it with: php examples/article-downhill-ski-analysis.php
 */

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\Freq;
use HiFolks\Statistics\NormalDist;
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\StreamingStat;

// === The Data ===
// 2026 Olympic Men's Downhill — 34 athletes, times in seconds.

$results = [
    ["name" => "Franjo von ALLMEN", "time" => 111.61],
    ["name" => "Giovanni FRANZONI", "time" => 111.81],
    ["name" => "Dominik PARIS", "time" => 112.11],
    ["name" => "Marco ODERMATT", "time" => 112.31],
    ["name" => "Alexis MONNEY", "time" => 112.36],
    ["name" => "Vincent KRIECHMAYR", "time" => 112.38],
    ["name" => "Daniel HEMETSBERGER", "time" => 112.58],
    ["name" => "Nils ALLEGRE", "time" => 112.8],
    ["name" => "James CRAWFORD", "time" => 113.0],
    ["name" => "Kyle NEGOMIR", "time" => 113.2],
    ["name" => "Mattia CASSE", "time" => 113.28],
    ["name" => "Miha HROBAT", "time" => 113.3],
    ["name" => "Bryce BENNETT", "time" => 113.45],
    ["name" => "Cameron ALEXANDER", "time" => 113.49],
    ["name" => "Raphael HAASER", "time" => 113.5],
    ["name" => "Martin CATER", "time" => 113.51],
    ["name" => "Florian SCHIEDER", "time" => 113.57],
    ["name" => "Ryan COCHRAN-SIEGLE", "time" => 113.63],
    ["name" => "Sam MORSE", "time" => 113.68],
    ["name" => "Elian LEHTO", "time" => 113.83],
    ["name" => "Simon JOCHER", "time" => 114.01],
    ["name" => "Nils ALPHAND", "time" => 114.06],
    ["name" => "Stefan ROGENTIN", "time" => 114.18],
    ["name" => "Jan ZABYSTRAN", "time" => 114.39],
    ["name" => "Jeffrey READ", "time" => 114.56],
    ["name" => "Stefan BABINSKY", "time" => 114.73],
    ["name" => "Alban ELEZI CANNAFERINA", "time" => 114.9],
    ["name" => "Brodie SEGER", "time" => 114.96],
    ["name" => "Marco PFIFFNER", "time" => 115.66],
    ["name" => "Barnabas SZOLLOS", "time" => 117.03],
    ["name" => "Arnaud ALESSANDRIA", "time" => 117.15],
    ["name" => "Elvis OPMANIS", "time" => 119.24],
    ["name" => "Dmytro SHEPIUK", "time" => 120.11],
    ["name" => "Cormac COMERFORD", "time" => 124.4],
];

$times = array_column($results, "time");

// =====================================================================
// Step 1: Descriptive Statistics
// =====================================================================
echo "=== Step 1: Descriptive Statistics ===" . PHP_EOL . PHP_EOL;

$mean = Stat::mean($times);
$median = Stat::median($times);
$std = Stat::stdev($times);
$min = min($times);
$max = max($times);
$range = $max - $min;
$quartiles = Stat::quantiles($times);

echo "Sample size: " . count($times) . PHP_EOL;
echo "Mean time:   " . round($mean, 2) . " seconds" . PHP_EOL;
echo "Median time: " . round($median, 2) . " seconds" . PHP_EOL;
echo "Std dev:     " . round($std, 2) . " seconds" . PHP_EOL;
echo "Min: " . $min . "s | Max: " . $max . "s | Range: " . round($range, 2) . "s" . PHP_EOL;
echo "Quartiles (Q1, Q2, Q3): "
    . round($quartiles[0], 2) . "s, "
    . round($quartiles[1], 2) . "s, "
    . round($quartiles[2], 2) . "s"
    . PHP_EOL;
echo PHP_EOL;

echo "Observations:" . PHP_EOL;
echo "- The mean (114.38) is higher than the median (113.60) — right skew." . PHP_EOL;
echo "- The range (12.79s) is large relative to the std dev (2.60s)." . PHP_EOL;
echo "- Q1 to Q3 spans only ~1.82s, so the middle 50% is tightly packed." . PHP_EOL;

// =====================================================================
// Step 1b: Robust Central Tendency
// =====================================================================
echo PHP_EOL . "=== Step 1b: Robust Central Tendency ===" . PHP_EOL . PHP_EOL;

$trimmedMean10 = Stat::trimmedMean($times, 0.1, 2);
$trimmedMean20 = Stat::trimmedMean($times, 0.2, 2);

echo "Regular mean:       " . round(Stat::mean($times), 2) . "s" . PHP_EOL;
echo "Trimmed mean (10%): " . $trimmedMean10 . "s" . PHP_EOL;
echo "Trimmed mean (20%): " . $trimmedMean20 . "s" . PHP_EOL;
echo PHP_EOL;
echo "The trimmed mean removes extreme values from each end." . PHP_EOL;
echo "With 10% cut, the 3 fastest and 3 slowest are excluded." . PHP_EOL;
echo "Result: the 'typical' time drops from 114.38s to 113.91s." . PHP_EOL;

// =====================================================================
// Step 1c: Percentile Analysis
// =====================================================================
echo PHP_EOL . "=== Step 1c: Percentile Analysis ===" . PHP_EOL . PHP_EOL;

echo "P10: " . Stat::percentile($times, 10, 2) . "s — elite threshold" . PHP_EOL;
echo "P25: " . Stat::percentile($times, 25, 2) . "s — top quarter" . PHP_EOL;
echo "P50: " . Stat::percentile($times, 50, 2) . "s — median" . PHP_EOL;
echo "P75: " . Stat::percentile($times, 75, 2) . "s — bottom quarter" . PHP_EOL;
echo "P90: " . Stat::percentile($times, 90, 2) . "s — struggling" . PHP_EOL;
echo PHP_EOL;
echo "Notice: P75-P90 gap (3.4s) is much larger than P10-P25 gap (0.7s)." . PHP_EOL;
echo "This asymmetry IS the right skew, quantified." . PHP_EOL;

// =====================================================================
// Step 1d: Precision of the Mean
// =====================================================================
echo PHP_EOL . "=== Step 1d: Precision of the Mean (SEM) ===" . PHP_EOL . PHP_EOL;

$sem = Stat::sem($times, 2);
echo "SEM: " . $sem . "s" . PHP_EOL;
echo "95% confidence interval: "
    . round(Stat::mean($times) - 1.96 * $sem, 2) . "s to "
    . round(Stat::mean($times) + 1.96 * $sem, 2) . "s"
    . PHP_EOL;
echo PHP_EOL;
echo "With 34 athletes, we estimate the true mean within ~"
    . round($sem * 1.96, 2) . "s at 95% confidence." . PHP_EOL;

// =====================================================================
// Step 2: Fitting a Normal Distribution
// =====================================================================
echo PHP_EOL . "=== Step 2: Fitting a Normal Distribution ===" . PHP_EOL . PHP_EOL;

$normal = NormalDist::fromSamples($times);
echo "Estimated mu (mean):     " . $normal->getMeanRounded(2) . " seconds" . PHP_EOL;
echo "Estimated sigma (std):   " . $normal->getSigmaRounded(2) . " seconds" . PHP_EOL;
echo PHP_EOL;
echo "Model median: " . $normal->getMedianRounded(2) . "s" . PHP_EOL;
echo "Actual median: " . round($median, 2) . "s" . PHP_EOL;
echo "Difference: " . round($normal->getMedianRounded(2) - $median, 2) . "s" . PHP_EOL;
echo "(the right skew pulls the model median = mean upward)" . PHP_EOL;

// =====================================================================
// Step 3: Asking Probabilistic Questions
// =====================================================================
echo PHP_EOL . "=== Step 3: Probabilistic Questions ===" . PHP_EOL . PHP_EOL;

$target = 113.0;
$probUnder = $normal->cdfRounded($target, 4);
$actualUnder = count(array_filter($times, fn(float $t): bool => $t <= $target));
echo "Q: What is the probability of finishing in " . $target . "s or less?" . PHP_EOL;
echo "Model:  P(time <= " . $target . "s) = "
    . round($probUnder * 100, 1) . "%" . PHP_EOL;
echo "Actual: " . $actualUnder . "/" . count($times)
    . " = " . round(($actualUnder / count($times)) * 100, 1) . "%" . PHP_EOL;
echo "(the gap shows the effect of skewness on the normal model)" . PHP_EOL;
echo PHP_EOL;
echo "PDF at " . $target . "s = " . $normal->pdfRounded($target, 6) . PHP_EOL;

// =====================================================================
// Step 4: Performance Thresholds (Inverse CDF)
// =====================================================================
echo PHP_EOL . "=== Step 4: Performance Thresholds ===" . PHP_EOL . PHP_EOL;

$eliteThreshold = $normal->invCdfRounded(0.2, 2);
$slowThreshold = $normal->invCdfRounded(0.8, 2);
echo "Top 20% fastest (below):  " . $eliteThreshold . " seconds" . PHP_EOL;
echo "Slowest 20% (above):      " . $slowThreshold . " seconds" . PHP_EOL;

// =====================================================================
// Step 5: Z-scores
// =====================================================================
echo PHP_EOL . "=== Step 5: Z-scores ===" . PHP_EOL . PHP_EOL;

echo str_pad("Athlete", 30)
    . str_pad("Time", 10)
    . str_pad("Z-score", 10)
    . "Tier"
    . PHP_EOL;
echo str_repeat("-", 65) . PHP_EOL;

$tierDefinitions = [
    ["max" => 0.20, "label" => "Elite"],
    ["max" => 0.50, "label" => "Strong"],
    ["max" => 0.80, "label" => "Average"],
    ["max" => 1.00, "label" => "Below avg"],
];

foreach ($results as $r) {
    $time = $r["time"];
    $percentile = $normal->cdf($time);

    $tier = "Below avg";
    foreach ($tierDefinitions as $def) {
        if ($percentile <= $def["max"]) {
            $tier = $def["label"];
            break;
        }
    }

    $z = $normal->zscoreRounded($time, 2);
    $zFormatted = ($z >= 0 ? "+" : "") . number_format($z, 2);

    echo str_pad($r["name"], 30)
        . str_pad(number_format($time, 2) . "s", 10)
        . str_pad($zFormatted, 10)
        . $tier
        . PHP_EOL;
}

// =====================================================================
// Step 5b: Outlier Detection
// =====================================================================
echo PHP_EOL . "=== Step 5b: Outlier Detection ===" . PHP_EOL . PHP_EOL;

// Method 1: Z-score
echo "Method 1: Z-score based (threshold = 2.5)" . PHP_EOL;
$zscoreOutliers = Stat::outliers($times, 2.5);
if ($zscoreOutliers === []) {
    echo "  No outliers detected." . PHP_EOL;
} else {
    foreach ($zscoreOutliers as $time) {
        $name = "";
        foreach ($results as $r) {
            if ($r["time"] === $time) {
                $name = $r["name"];
                break;
            }
        }
        echo "  " . $time . "s — " . $name . PHP_EOL;
    }
}

// Method 2: IQR
echo PHP_EOL . "Method 2: IQR based (factor = 1.5, box plot whiskers)" . PHP_EOL;
$iqrOutliers = Stat::iqrOutliers($times);
if ($iqrOutliers === []) {
    echo "  No outliers detected." . PHP_EOL;
} else {
    foreach ($iqrOutliers as $time) {
        $name = "";
        foreach ($results as $r) {
            if ($r["time"] === $time) {
                $name = $r["name"];
                break;
            }
        }
        echo "  " . $time . "s — " . $name . PHP_EOL;
    }
}

echo PHP_EOL;
echo "Z-score detected 1 outlier; IQR detected 3." . PHP_EOL;
echo "IQR is more robust for skewed data — outliers don't inflate" . PHP_EOL;
echo "the detection threshold (unlike z-score, where they inflate stdev)." . PHP_EOL;

// =====================================================================
// Step 6: Classifying Athletes into Tiers
// =====================================================================
echo PHP_EOL . "=== Step 6: Athlete Tier Classification ===" . PHP_EOL . PHP_EOL;

echo "Using the normal model's CDF to assign tiers:" . PHP_EOL;
echo "  Elite:     bottom 20% of the CDF (fastest)" . PHP_EOL;
echo "  Strong:    20%–50%" . PHP_EOL;
echo "  Average:   50%–80%" . PHP_EOL;
echo "  Below avg: 80%–100% (slowest)" . PHP_EOL;
echo PHP_EOL;

$tierCounts = ["Elite" => 0, "Strong" => 0, "Average" => 0, "Below avg" => 0];
foreach ($results as $r) {
    $percentile = $normal->cdf($r["time"]);
    foreach ($tierDefinitions as $def) {
        if ($percentile <= $def["max"]) {
            $tierCounts[$def["label"]]++;
            break;
        }
    }
}
foreach ($tierCounts as $tier => $count) {
    echo str_pad($tier, 12) . str_repeat("*", $count) . " (" . $count . ")" . PHP_EOL;
}

// =====================================================================
// Step 7: Frequency Table
// =====================================================================
echo PHP_EOL . "=== Step 7: Frequency Table (1-second bins) ===" . PHP_EOL . PHP_EOL;

$freqTable = Freq::frequencyTableBySize($times, 1);
foreach ($freqTable as $class => $count) {
    echo str_pad($class . "s", 8)
        . str_repeat("*", $count)
        . " (" . $count . ")"
        . PHP_EOL;
}

// =====================================================================
// Step 8: Skewness and Kurtosis
// =====================================================================
echo PHP_EOL . "=== Step 8: Skewness and Kurtosis ===" . PHP_EOL . PHP_EOL;

echo "Skewness: " . Stat::skewness($times, 4) . PHP_EOL;
echo "  (positive = right-skewed, a few slow finishers pull the tail)" . PHP_EOL;
echo "Kurtosis: " . Stat::kurtosis($times, 4) . PHP_EOL;
echo "  (positive = heavy tails, outliers present)" . PHP_EOL;

// =====================================================================
// Step 9: Dispersion Beyond Standard Deviation
// =====================================================================
echo PHP_EOL . "=== Step 9: Dispersion Measures Compared ===" . PHP_EOL . PHP_EOL;

$stdev    = Stat::stdev($times, 4);
$mad      = Stat::meanAbsoluteDeviation($times, 4);
$medianAD = Stat::medianAbsoluteDeviation($times, 4);

echo "Standard deviation:        " . $stdev . "s" . PHP_EOL;
echo "Mean Absolute Deviation:   " . $mad . "s" . PHP_EOL;
echo "Median Absolute Deviation: " . $medianAD . "s" . PHP_EOL;
echo PHP_EOL;
echo "The median absolute deviation (0.88s) is much smaller than" . PHP_EOL;
echo "the stdev (2.60s). This reveals two groups: a tight core pack" . PHP_EOL;
echo "(within ~1 second of each other) and a few stragglers." . PHP_EOL;

// =====================================================================
// Step 10: Coefficient of Variation
// =====================================================================
echo PHP_EOL . "=== Step 10: Coefficient of Variation ===" . PHP_EOL . PHP_EOL;

$cvFull  = Stat::coefficientOfVariation($times, 2);
$top10   = array_slice($times, 0, 10);
$cvTop10 = Stat::coefficientOfVariation($top10, 2);

echo "Full field CV: " . $cvFull . "%" . PHP_EOL;
echo "Top 10 CV:     " . $cvTop10 . "%" . PHP_EOL;
echo PHP_EOL;
echo "The top 10 is 5x tighter than the full field." . PHP_EOL;
echo "CV lets you compare tightness across different events or years." . PHP_EOL;

// =====================================================================
// Step 11: Weighted Median
// =====================================================================
echo PHP_EOL . "=== Step 11: Weighted Median ===" . PHP_EOL . PHP_EOL;

$weights = [];
foreach ($results as $i => $r) {
    $weights[] = $i < 15 ? 3.0 : 1.0;
}
$wMedian = Stat::weightedMedian($times, $weights, 2);

echo "Regular median:  " . round(Stat::median($times), 2) . "s" . PHP_EOL;
echo "Weighted median: " . $wMedian . "s  (top-15 seeded athletes weighted 3x)" . PHP_EOL;
echo PHP_EOL;
echo "The weighted median answers: 'What does a competitive time look like?'" . PHP_EOL;
echo "rather than 'What does the typical time look like?'" . PHP_EOL;

// =====================================================================
// Step 12: StreamingStat — Real-Time Processing
// =====================================================================
echo PHP_EOL . "=== Step 12: StreamingStat (O(1) Memory) ===" . PHP_EOL . PHP_EOL;

$stream = new StreamingStat();

foreach ($results as $i => $r) {
    $stream->add($r["time"]);

    if (in_array($i + 1, [5, 10, 20, 34])) {
        echo "After " . str_pad($stream->count(), 2) . " athletes: "
            . "mean=" . $stream->mean(2) . "s, "
            . "stdev=" . $stream->stdev(2) . "s, "
            . "min=" . $stream->min() . "s, "
            . "max=" . $stream->max() . "s"
            . PHP_EOL;
    }
}

echo PHP_EOL;
echo "Final streaming results match Stat:" . PHP_EOL;
echo "  Streaming mean:  " . $stream->mean(2) . "s  vs  Stat::mean: " . round($mean, 2) . "s" . PHP_EOL;
echo "  Streaming stdev: " . $stream->stdev(2) . "s  vs  Stat::stdev: " . round($std, 2) . "s" . PHP_EOL;

// =====================================================================
// When the Normal Distribution Works (and When It Doesn't)
// =====================================================================
echo PHP_EOL . "=== Model Limitations ===" . PHP_EOL . PHP_EOL;

echo "The normal model is a useful approximation, but this data is" . PHP_EOL;
echo "right-skewed (skewness: " . Stat::skewness($times, 2) . "). Signs of misfit:" . PHP_EOL;
echo "- Model median (" . $normal->getMedianRounded(2)
    . "s) differs from actual median (" . round($median, 2) . "s)" . PHP_EOL;
echo "- Model P(time <= 113s) = " . round($normal->cdf(113.0) * 100, 1)
    . "%, actual = " . round((count(array_filter($times, fn(float $t): bool => $t <= 113.0)) / count($times)) * 100, 1) . "%" . PHP_EOL;
echo "- Kurtosis (" . Stat::kurtosis($times, 2) . ") >> 0 — heavier tails than normal" . PHP_EOL;
echo PHP_EOL;
echo "For this dataset, robust measures (trimmed mean, IQR outliers," . PHP_EOL;
echo "median absolute deviation) give more reliable insights than" . PHP_EOL;
echo "methods that assume normality." . PHP_EOL;

// =====================================================================
// Summary
// =====================================================================
echo PHP_EOL . str_repeat("=", 55) . PHP_EOL;
echo "SUMMARY" . PHP_EOL;
echo str_repeat("=", 55) . PHP_EOL;
echo "Winner:              " . $results[0]["name"] . " (" . $results[0]["time"] . "s)" . PHP_EOL;
echo "Mean / Median:       " . round($mean, 2) . "s / " . round($median, 2) . "s" . PHP_EOL;
echo "Trimmed mean (10%):  " . $trimmedMean10 . "s" . PHP_EOL;
echo "Core pack spread:    " . $medianAD . "s (median abs deviation)" . PHP_EOL;
echo "Race tightness (CV): " . $cvFull . "% (full field), " . $cvTop10 . "% (top 10)" . PHP_EOL;
echo "Outliers (IQR):      " . count($iqrOutliers) . " athletes flagged" . PHP_EOL;
echo "Distribution:        right-skewed (skewness " . Stat::skewness($times, 2) . ")" . PHP_EOL;
