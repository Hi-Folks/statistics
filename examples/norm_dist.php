<?php

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\Freq;
use HiFolks\Statistics\NormalDist;
use HiFolks\Statistics\Stat;

/**
 * This is the result of the Downhill race at Olympic Games 2026.
 * The results are stored in an array with name and the time in
 * seconds.
 */
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

// --- Descriptive Statistics ---
echo "=== Downhill Race Analysis - Olympic Games 2026 ===" . PHP_EOL . PHP_EOL;

$mean = Stat::mean($times);
$median = Stat::median($times);
$std = Stat::stdev($times);
$min = min($times);
$max = max($times);
$range = $max - $min;
$quartiles = Stat::quantiles($times);

echo "Sample size: " . count($times) . PHP_EOL;
echo "Mean time: " . round($mean, 2) . " seconds" . PHP_EOL;
echo "Median time: " . round($median, 2) . " seconds" . PHP_EOL;
echo "Standard deviation: " . round($std, 2) . " seconds" . PHP_EOL;
echo "Min: " .
    $min .
    "s | Max: " .
    $max .
    "s | Range: " .
    round($range, 2) .
    "s" .
    PHP_EOL;
echo "Quartiles (Q1, Q2, Q3): " .
    round($quartiles[0], 2) .
    "s, " .
    round($quartiles[1], 2) .
    "s, " .
    round($quartiles[2], 2) .
    "s" .
    PHP_EOL;

// --- Normal Distribution Model ---
echo PHP_EOL . "=== Normal Distribution Model ===" . PHP_EOL . PHP_EOL;

$normal = NormalDist::fromSamples($times);
echo "Estimated mu (mean): " .
    $normal->getMeanRounded(2) .
    " seconds" .
    PHP_EOL;
echo "Estimated sigma (std dev): " .
    $normal->getSigmaRounded(2) .
    " seconds" .
    PHP_EOL;

// Compare model median vs actual median
// For a normal distribution, median = mean, so getMedian() returns mu directly.
echo "Model median: " . $normal->getMedianRounded(2) . " seconds" . PHP_EOL;
echo "Actual median: " . round($median, 2) . " seconds" . PHP_EOL;

// Note: the model median equals the mean (as expected for a normal
// distribution), but it differs from the actual median by
// ~0.78 seconds. This gap tells us the data is right-skewed:
// a few very slow finishers (119s, 120s, 124s) pull the mean up.
// A normal distribution assumes symmetry, so it is not a perfect
// fit for this dataset.

// --- Thresholds from the model ---
echo PHP_EOL . "=== Performance Thresholds ===" . PHP_EOL . PHP_EOL;

$eliteThreshold = $normal->invCdfRounded(0.2, 2);
$slowThreshold = $normal->invCdfRounded(0.8, 2);
echo "Top 20% fastest (below): " . $eliteThreshold . " seconds" . PHP_EOL;
echo "Slowest 20% (above): " . $slowThreshold . " seconds" . PHP_EOL;

// --- Probability questions ---
echo PHP_EOL . "=== Probability Questions ===" . PHP_EOL . PHP_EOL;

$target = 113.0;
$probUnder = $normal->cdfRounded($target, 4);
$actualUnder = count(array_filter($times, fn(float $t): bool => $t <= $target));
echo "Model: P(time <= " .
    $target .
    "s) = " .
    round($probUnder * 100, 1) .
    "%" .
    PHP_EOL;
echo "Actual: " .
    $actualUnder .
    "/" .
    count($times) .
    " = " .
    round(($actualUnder / count($times)) * 100, 1) .
    "%" .
    PHP_EOL;
echo "(The gap shows the effect of skewness on the normal model)" . PHP_EOL;

$pdfAt = $normal->pdfRounded($target, 6);
echo "PDF at " . $target . "s = " . $pdfAt . PHP_EOL;

// --- Athlete Tier Classification ---
echo PHP_EOL . "=== Athlete Tier Classification ===" . PHP_EOL . PHP_EOL;

// We use percentile ranks based on the normal model.
// Lower time = better performance = lower percentile.
$tierDefinitions = [
    ["max" => 0.2, "label" => "Elite"],
    ["max" => 0.5, "label" => "Strong"],
    ["max" => 0.8, "label" => "Average"],
    ["max" => 1.0, "label" => "Below avg"],
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

    echo str_pad($r["name"], 30) .
        str_pad(number_format($time, 2) . "s", 10) .
        str_pad($tier, 12) .
        "z: " .
        str_pad($zFormatted, 7) .
        "(percentile: " .
        min(round($percentile * 100, 1), 99.9) .
        "%)" .
        PHP_EOL;
}

// --- Frequency Table ---
echo PHP_EOL . "=== Frequency Table (2-second classes) ===" . PHP_EOL . PHP_EOL;

$freqTable = Freq::frequencyTableBySize($times, 1);
foreach ($freqTable as $class => $count) {
    echo str_pad($class . "s", 8) .
        str_repeat("*", $count) .
        " (" .
        $count .
        ")" .
        PHP_EOL;
}
