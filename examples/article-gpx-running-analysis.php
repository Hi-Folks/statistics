<?php

/**
 * Analyze Your Running Performance with GPX Data and PHP Statistics
 *
 * This script shows how to parse a GPX file from your sport watch
 * and analyze your running performance using the hi-folks/statistics package.
 *
 * It includes helper functions for GPX parsing, plus simulated data
 * so you can run it immediately without a GPX file.
 *
 * Run it with: php examples/article-gpx-running-analysis.php
 */

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\Freq;
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Utils\Arr;
use HiFolks\Statistics\Utils\Format;

// ============================================================
// HELPER FUNCTIONS — GPX parsing and distance calculation
// ============================================================

/**
 * Parse a GPX file and return an array of trackpoints.
 * Each trackpoint: ['lat' => float, 'lon' => float, 'ele' => float,
 *                   'time' => int (unix timestamp), 'hr' => int|null]
 */
function parseGpx(string $filePath): array
{
    $xml = simplexml_load_file($filePath);
    if ($xml === false) {
        throw new RuntimeException("Cannot parse GPX file: {$filePath}");
    }

    $namespaces = $xml->getNamespaces(true);
    $points = [];
    foreach ($xml->trk->trkseg->trkpt as $trkpt) {
        $point = [
            "lat" => (float) $trkpt["lat"],
            "lon" => (float) $trkpt["lon"],
            "ele" =>
                property_exists($trkpt, "ele") && $trkpt->ele !== null
                    ? (float) $trkpt->ele
                    : 0.0,
            "time" =>
                property_exists($trkpt, "time") && $trkpt->time !== null
                    ? strtotime((string) $trkpt->time)
                    : 0,
            "hr" => null,
        ];

        // Try to extract heart rate from Garmin TrackPointExtension
        if (isset($namespaces["gpxtpx"])) {
            $extensions = $trkpt->extensions;
            if ($extensions) {
                $gpxtpx = $extensions->children($namespaces["gpxtpx"]);
                if (
                    property_exists($gpxtpx->TrackPointExtension, "hr") &&
                    $gpxtpx->TrackPointExtension->hr !== null
                ) {
                    $point["hr"] = (int) $gpxtpx->TrackPointExtension->hr;
                }
            }
        }

        $points[] = $point;
    }

    return $points;
}

/**
 * Haversine distance between two GPS coordinates in meters.
 */
function haversineDistance(
    float $lat1,
    float $lon1,
    float $lat2,
    float $lon2,
): float {
    $R = 6371000; // Earth radius in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a =
        sin($dLat / 2) ** 2 +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

/**
 * Build per-kilometer splits from trackpoints.
 * Returns array of ['km' => int, 'time' => int (seconds), 'pace' => int (sec/km),
 *                    'eleGain' => float, 'eleLoss' => float, 'avgHr' => int|null]
 */
function buildKmSplits(array $trackpoints): array
{
    $splits = [];
    $currentKm = 1;
    $kmDistance = 0;
    $kmStartTime = $trackpoints[0]["time"];
    $kmEleGain = 0;
    $kmEleLoss = 0;
    $kmHrValues = [];
    $counter = count($trackpoints);

    for ($i = 1; $i < $counter; $i++) {
        $prev = $trackpoints[$i - 1];
        $curr = $trackpoints[$i];

        $segDist = haversineDistance(
            $prev["lat"],
            $prev["lon"],
            $curr["lat"],
            $curr["lon"],
        );
        $kmDistance += $segDist;

        $eleDiff = $curr["ele"] - $prev["ele"];
        if ($eleDiff > 0) {
            $kmEleGain += $eleDiff;
        } else {
            $kmEleLoss += abs($eleDiff);
        }

        if ($curr["hr"] !== null) {
            $kmHrValues[] = $curr["hr"];
        }

        if ($kmDistance >= 1000) {
            $kmTime = $curr["time"] - $kmStartTime;
            $splits[] = [
                "km" => $currentKm,
                "time" => $kmTime,
                "pace" => $kmTime,
                "eleGain" => round($kmEleGain, 1),
                "eleLoss" => round($kmEleLoss, 1),
                "avgHr" =>
                    count($kmHrValues) > 0
                        ? (int) round(Stat::mean($kmHrValues))
                        : null,
            ];

            $currentKm++;
            $kmDistance -= 1000;
            $kmStartTime = $curr["time"];
            $kmEleGain = 0;
            $kmEleLoss = 0;
            $kmHrValues = [];
        }
    }

    return $splits;
}

/**
 * Format a pace in seconds as "M:SS/km".
 */
function formatPace(int|float $seconds): string
{
    return Format::secondsToTime((int) round($seconds)) . "/km";
}

// ============================================================
// THE DATA
// ============================================================

// === Option 1: Parse a real GPX file ===
// Uncomment these lines if you have a GPX file from your sport watch:
//

// === Option 2: Simulated 10K run ===
// A realistic 10K with a hilly middle section, slight positive split,
// and heart rate drifting upward as fatigue accumulates.
$splits = [
    [
        "km" => 1,
        "time" => 322,
        "pace" => 322,
        "eleGain" => 5,
        "eleLoss" => 2,
        "avgHr" => 145,
    ],
    [
        "km" => 2,
        "time" => 318,
        "pace" => 318,
        "eleGain" => 8,
        "eleLoss" => 3,
        "avgHr" => 150,
    ],
    [
        "km" => 3,
        "time" => 335,
        "pace" => 335,
        "eleGain" => 22,
        "eleLoss" => 4,
        "avgHr" => 158,
    ],
    [
        "km" => 4,
        "time" => 348,
        "pace" => 348,
        "eleGain" => 28,
        "eleLoss" => 5,
        "avgHr" => 164,
    ],
    [
        "km" => 5,
        "time" => 340,
        "pace" => 340,
        "eleGain" => 15,
        "eleLoss" => 18,
        "avgHr" => 162,
    ],
    [
        "km" => 6,
        "time" => 312,
        "pace" => 312,
        "eleGain" => 2,
        "eleLoss" => 30,
        "avgHr" => 155,
    ],
    [
        "km" => 7,
        "time" => 325,
        "pace" => 325,
        "eleGain" => 3,
        "eleLoss" => 8,
        "avgHr" => 158,
    ],
    [
        "km" => 8,
        "time" => 338,
        "pace" => 338,
        "eleGain" => 12,
        "eleLoss" => 5,
        "avgHr" => 165,
    ],
    [
        "km" => 9,
        "time" => 352,
        "pace" => 352,
        "eleGain" => 18,
        "eleLoss" => 3,
        "avgHr" => 170,
    ],
    [
        "km" => 10,
        "time" => 330,
        "pace" => 330,
        "eleGain" => 4,
        "eleLoss" => 15,
        "avgHr" => 172,
    ],
];

$gpxFile = "/Users/roberto.butti/Downloads/activity_21126428365.gpx";
if (file_exists($gpxFile)) {
    $trackpoints = parseGpx($gpxFile);
    if ($trackpoints !== []) {
        echo "Using File: " . $gpxFile . PHP_EOL;
        $splits = buildKmSplits($trackpoints);
    }
}

// Extract column arrays we will reuse throughout
[$paces, $eleGains, $hrValues, $kmNumbers] = Arr::extract($splits, [
    "pace",
    "eleGain",
    "avgHr",
    "km",
]);

// ============================================================
// STEP 1: Run Overview
// ============================================================

$totalDistance = count($splits);
$totalTime = array_sum(array_column($splits, "time"));
$totalEleGain = array_sum(array_column($splits, "eleGain"));
$totalEleLoss = array_sum(array_column($splits, "eleLoss"));

echo "=== STEP 1: Run Overview ===" . PHP_EOL;
echo "Distance:        " . $totalDistance . " km" . PHP_EOL;
echo "Total time:      " . Format::secondsToTime($totalTime) . PHP_EOL;
echo "Average pace:    " . formatPace(Stat::mean($paces)) . PHP_EOL;
echo "Elevation gain:  +" . $totalEleGain . " m" . PHP_EOL;
echo "Elevation loss:  -" . $totalEleLoss . " m" . PHP_EOL;
echo "Average HR:      " . round(Stat::mean($hrValues)) . " bpm" . PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 2: Pace Descriptive Statistics
// ============================================================

$meanPace = Stat::mean($paces);
$medianPace = Stat::median($paces);
$stdevPace = Stat::stdev($paces);
$quartiles = Stat::quantiles($paces);

echo "=== STEP 2: Pace Descriptive Statistics ===" . PHP_EOL;
echo "Mean pace:       " . formatPace($meanPace) . PHP_EOL;
echo "Median pace:     " . formatPace($medianPace) . PHP_EOL;
echo "Std deviation:   " . round($stdevPace, 1) . " sec" . PHP_EOL;
echo "Fastest km:      " .
    formatPace(min($paces)) .
    " (km " .
    $splits[array_search(min($paces), $paces)]["km"] .
    ")" .
    PHP_EOL;
echo "Slowest km:      " .
    formatPace(max($paces)) .
    " (km " .
    $splits[array_search(max($paces), $paces)]["km"] .
    ")" .
    PHP_EOL;
echo "Quartiles:       Q1=" .
    formatPace($quartiles[0]) .
    "  Q2=" .
    formatPace($quartiles[1]) .
    "  Q3=" .
    formatPace($quartiles[2]) .
    PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 3: Pacing Consistency
// ============================================================

$cv = Stat::coefficientOfVariation($paces, 2);
$halfPoint = intdiv(count($splits), 2);
$firstHalfPaces = array_slice($paces, 0, $halfPoint);
$secondHalfPaces = array_slice($paces, $halfPoint);
$meanFirst = Stat::mean($firstHalfPaces);
$meanSecond = Stat::mean($secondHalfPaces);
$splitDiff = $meanSecond - $meanFirst;
$splitPct = round(($splitDiff / $meanFirst) * 100, 1);

echo "=== STEP 3: Pacing Consistency ===" . PHP_EOL;
echo "Coefficient of Variation: " . $cv . "%" . PHP_EOL;
echo "First half avg pace:  " .
    formatPace($meanFirst) .
    " (km 1-" .
    $halfPoint .
    ")" .
    PHP_EOL;
echo "Second half avg pace: " .
    formatPace($meanSecond) .
    " (km " .
    ($halfPoint + 1) .
    "-" .
    $totalDistance .
    ")" .
    PHP_EOL;
if ($splitDiff > 0) {
    echo "Positive split: +" .
        round($splitDiff, 1) .
        " sec/km slower (" .
        $splitPct .
        "% fade)" .
        PHP_EOL;
} elseif ($splitDiff < 0) {
    echo "Negative split: " .
        round(abs($splitDiff), 1) .
        " sec/km faster (" .
        abs($splitPct) .
        "% improvement)" .
        PHP_EOL;
} else {
    echo "Even split: perfectly consistent pacing" . PHP_EOL;
}
echo PHP_EOL;

// ============================================================
// STEP 4: Elevation Impact on Pace
// ============================================================

$corrEle = Stat::correlation($eleGains, $paces);
$regEle = Stat::linearRegression($eleGains, $paces);
$r2Ele = Stat::rSquared($eleGains, $paces, false, 4);

echo "=== STEP 4: Elevation Impact on Pace ===" . PHP_EOL;
echo "Correlation (elevation gain vs pace): " . round($corrEle, 4) . PHP_EOL;
echo "Linear regression: pace = " .
    round($regEle[0], 2) .
    " x eleGain + " .
    round($regEle[1], 1) .
    PHP_EOL;
echo "R-squared: " . $r2Ele . PHP_EOL;
echo "Interpretation: each meter of elevation gain costs ~" .
    round($regEle[0], 1) .
    " seconds per km" .
    PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 5: Heart Rate Analysis
// ============================================================

$meanHr = Stat::mean($hrValues);
$medianHr = Stat::median($hrValues);
$stdevHr = Stat::stdev($hrValues);

// Cardiac drift: does HR rise over the course of the run?
$corrHrKm = Stat::correlation($kmNumbers, $hrValues);
$regHrKm = Stat::linearRegression($kmNumbers, $hrValues);
$r2HrKm = Stat::rSquared($kmNumbers, $hrValues, false, 4);

// HR vs pace correlation
$corrHrPace = Stat::correlation($hrValues, $paces);

echo "=== STEP 5: Heart Rate Analysis ===" . PHP_EOL;
echo "Mean HR:    " . round($meanHr) . " bpm" . PHP_EOL;
echo "Median HR:  " . round($medianHr) . " bpm" . PHP_EOL;
echo "Std dev:    " . round($stdevHr, 1) . " bpm" . PHP_EOL;
echo "Min HR:     " .
    min($hrValues) .
    " bpm | Max HR: " .
    max($hrValues) .
    " bpm" .
    PHP_EOL;
echo PHP_EOL;

echo "Cardiac drift (HR vs km):" . PHP_EOL;
echo "  Correlation:      " . round($corrHrKm, 4) . PHP_EOL;
echo "  Regression:       HR = " .
    round($regHrKm[0], 2) .
    " x km + " .
    round($regHrKm[1], 1) .
    PHP_EOL;
echo "  R-squared:        " . $r2HrKm . PHP_EOL;
echo "  HR drift per km:  +" . round($regHrKm[0], 1) . " bpm/km" . PHP_EOL;
echo PHP_EOL;

echo "HR vs pace correlation: " . round($corrHrPace, 4) . PHP_EOL;
echo PHP_EOL;

// Heart rate zone distribution
$hrZones = Freq::frequencyTableBySize($hrValues, 10);
echo "Heart Rate Zone Distribution:" . PHP_EOL;
foreach ($hrZones as $range => $count) {
    echo "  " .
        $range .
        " bpm: " .
        str_repeat("#", $count) .
        " (" .
        $count .
        " km)" .
        PHP_EOL;
}
echo PHP_EOL;

// ============================================================
// STEP 6: Outlier Detection
// ============================================================

$zscores = Stat::zscores($paces, 2);
$zOutliers = Stat::outliers($paces, 2.0);
$iqrOutliers = Stat::iqrOutliers($paces);

echo "=== STEP 6: Outlier Detection ===" . PHP_EOL;
echo "Per-km z-scores (negative = faster than average):" . PHP_EOL;
foreach ($splits as $i => $split) {
    $z = $zscores[$i];
    $bar =
        $z < 0
            ? str_repeat("<", (int) abs(round($z * 5)))
            : str_repeat(">", (int) round($z * 5));
    echo "  km " .
        str_pad((string) $split["km"], 2, " ", STR_PAD_LEFT) .
        ": " .
        formatPace($split["pace"]) .
        "  z=" .
        sprintf("%+.2f", $z) .
        "  " .
        $bar .
        PHP_EOL;
}
echo PHP_EOL;
echo "Z-score outliers (|z| > 2.0): " .
    (count($zOutliers) > 0
        ? implode(", ", array_map(formatPace(...), $zOutliers))
        : "none") .
    PHP_EOL;
echo "IQR outliers:                 " .
    (count($iqrOutliers) > 0
        ? implode(", ", array_map(formatPace(...), $iqrOutliers))
        : "none") .
    PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 7: Percentile Benchmarks
// ============================================================

echo "=== STEP 7: Percentile Benchmarks ===" . PHP_EOL;
echo "Your pace distribution across this run:" . PHP_EOL;
$percentiles = [10, 25, 50, 75, 90];
foreach ($percentiles as $p) {
    $val = Stat::percentile($paces, $p, 0);
    echo "  P" .
        str_pad((string) $p, 2, " ", STR_PAD_LEFT) .
        ": " .
        formatPace($val) .
        PHP_EOL;
}
echo PHP_EOL;
echo "P10 = your fastest 10% of km were at this pace or faster" . PHP_EOL;
echo "P90 = your slowest 10% of km were at this pace or slower" . PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 8: Distribution Shape
// ============================================================

$skewness = Stat::skewness($paces, 4);
$kurtosis = Stat::kurtosis($paces, 4);

echo "=== STEP 8: Distribution Shape ===" . PHP_EOL;
echo "Skewness: " . $skewness . PHP_EOL;
echo "Kurtosis: " . $kurtosis . PHP_EOL;
if ($skewness > 0.2) {
    echo "Right-skewed: you have a tail of slower km (hills? fatigue?)" .
        PHP_EOL;
} elseif ($skewness < -0.2) {
    echo "Left-skewed: you have a tail of faster km (downhills? strong start?)" .
        PHP_EOL;
} else {
    echo "Approximately symmetric pacing" . PHP_EOL;
}
echo PHP_EOL;

// ============================================================
// STEP 9: Confidence Interval on True Pace
// ============================================================

$ci = Stat::confidenceInterval($paces, 0.95, 0);
$sem = Stat::sem($paces, 1);

echo "=== STEP 9: Confidence Interval ===" . PHP_EOL;
echo "95% CI for your true pace: " .
    formatPace($ci[0]) .
    " to " .
    formatPace($ci[1]) .
    PHP_EOL;
echo "Standard Error of the Mean: " . $sem . " sec" . PHP_EOL;
echo "With more km (longer runs), this interval would narrow." . PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 10: Multi-Run Trend Analysis (Simulated)
// ============================================================

// Simulated: 8 weeks of average 10K paces showing diminishing improvement
// Early weeks show big gains; later weeks show smaller improvements (plateau effect)
$weeks = [1, 2, 3, 4, 5, 6, 7, 8];
$weeklyPaces = [350, 342, 337, 333, 330, 328, 326, 325];

$trendReg = Stat::linearRegression($weeks, $weeklyPaces);
$trendR2 = Stat::rSquared($weeks, $weeklyPaces, false, 4);
$trendCorr = Stat::correlation($weeks, $weeklyPaces);

echo "=== STEP 10: Multi-Run Trend (8-Week Simulation) ===" . PHP_EOL;
echo "Weekly average paces:" . PHP_EOL;
foreach ($weeks as $i => $w) {
    echo "  Week " . $w . ": " . formatPace($weeklyPaces[$i]) . PHP_EOL;
}
echo PHP_EOL;
echo "Trend regression: pace = " .
    round($trendReg[0], 2) .
    " x week + " .
    round($trendReg[1], 1) .
    PHP_EOL;
echo "R-squared:        " . $trendR2 . PHP_EOL;
echo "Correlation:      " . round($trendCorr, 4) . PHP_EOL;
echo "Improvement rate:  " .
    round(abs($trendReg[0]), 1) .
    " seconds/km per week" .
    PHP_EOL;
echo PHP_EOL;

// Linear prediction for week 12
$linearPrediction12 = $trendReg[0] * 12 + $trendReg[1];
echo "Linear prediction at week 12: " .
    formatPace(max(0, $linearPrediction12)) .
    PHP_EOL;
echo "(Extrapolation — use with caution!)" . PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 10b: Logarithmic Regression — Modeling the Plateau
// ============================================================

echo "=== STEP 10b: Logarithmic Regression ===" . PHP_EOL;
echo PHP_EOL;

// Logarithmic model: pace = a * ln(week) + b
$logReg = Stat::logarithmicRegression($weeks, $weeklyPaces);
$logWeeks = array_map(log(...), $weeks);
$logR2 = Stat::rSquared($logWeeks, $weeklyPaces, false, 4);

echo "Logarithmic regression: pace = " .
    round($logReg[0], 2) .
    " x ln(week) + " .
    round($logReg[1], 1) .
    PHP_EOL;
echo "R-squared:              " . $logR2 . PHP_EOL;
echo PHP_EOL;

// Compare models
echo "Model comparison:" . PHP_EOL;
echo "  Linear R²:      " . $trendR2 . PHP_EOL;
echo "  Logarithmic R²: " . $logR2 . PHP_EOL;
echo "  Better fit:      " .
    ($logR2 > $trendR2 ? "Logarithmic" : "Linear") .
    PHP_EOL;
echo PHP_EOL;

// Compare predictions
$logPrediction12 = $logReg[0] * log(12) + $logReg[1];
$logPrediction20 = $logReg[0] * log(20) + $logReg[1];
$linearPrediction20 = $trendReg[0] * 20 + $trendReg[1];

echo "Predictions:" . PHP_EOL;
echo "  Week 12 — Linear: " .
    formatPace(max(0, $linearPrediction12)) .
    "  |  Logarithmic: " .
    formatPace(max(0, $logPrediction12)) .
    PHP_EOL;
echo "  Week 20 — Linear: " .
    formatPace(max(0, $linearPrediction20)) .
    "  |  Logarithmic: " .
    formatPace(max(0, $logPrediction20)) .
    PHP_EOL;
echo PHP_EOL;
echo "The logarithmic model predicts more conservative (realistic) paces" .
    PHP_EOL;
echo "because it accounts for the natural plateau in athletic improvement." .
    PHP_EOL;
echo PHP_EOL;

// ============================================================
// STEP 10c: All Four Models Compared
// ============================================================

echo "=== STEP 10c: All Four Models Compared ===" . PHP_EOL;
echo PHP_EOL;

// Power: pace = a * week^b
[$aPow, $bPow] = Stat::powerRegression($weeks, $weeklyPaces);
$logPaces = array_map(log(...), $weeklyPaces);
$r2Pow = Stat::rSquared($logWeeks, $logPaces, false, 4);

// Exponential: pace = a * e^(b * week)
[$aExp, $bExp] = Stat::exponentialRegression($weeks, $weeklyPaces);
$r2Exp = Stat::rSquared($weeks, $logPaces, false, 4);

// Predictions for week 12, 20, 52
$predWeeks = [12, 20, 52];
$models = [
    "Linear" => [
        "r2" => $trendR2,
        "predict" => fn($w): int|float => $trendReg[0] * $w + $trendReg[1],
    ],
    "Logarithmic" => [
        "r2" => $logR2,
        "predict" => fn($w): float => $logReg[0] * log($w) + $logReg[1],
    ],
    "Power" => [
        "r2" => $r2Pow,
        "predict" => fn($w): float|int => $aPow * $w ** $bPow,
    ],
    "Exponential" => [
        "r2" => $r2Exp,
        "predict" => fn($w): float => $aExp * exp($bExp * $w),
    ],
];

echo str_pad("Model", 18) .
    str_pad("R²", 11) .
    str_pad("Week 12", 11) .
    str_pad("Week 20", 11) .
    "Week 52" .
    PHP_EOL;
echo str_repeat("-", 58) . PHP_EOL;

foreach ($models as $name => $model) {
    echo str_pad($name, 18) .
        str_pad((string) $model["r2"], 11) .
        str_pad(formatPace(max(0, $model["predict"](12))), 11) .
        str_pad(formatPace(max(0, $model["predict"](20))), 11) .
        formatPace(max(0, $model["predict"](52))) .
        PHP_EOL;
}
echo PHP_EOL;

// Find the best model by R²
$bestModel = "";
$bestR2 = 0;
foreach ($models as $name => $model) {
    if ($model["r2"] > $bestR2) {
        $bestR2 = $model["r2"];
        $bestModel = $name;
    }
}
echo "Best fit by R²: " . $bestModel . " (R² = " . $bestR2 . ")" . PHP_EOL;
echo "The data tells us the improvement pattern follows a curve, not a straight line." .
    PHP_EOL;
