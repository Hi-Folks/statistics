<?php

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\Enums\KdeKernel;
use HiFolks\Statistics\Stat;

/**
 * Kernel Density Estimation applied to real sports data.
 *
 * Dataset: Men's Downhill results — Winter Olympic Games 2026.
 *
 * KDE lets us move beyond simple averages and histograms to answer
 * richer questions: Where do finishing times cluster?  What is the
 * probability of finishing under a given threshold?  How would
 * simulated future races look?
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

echo "=== Men's Downhill — Olympic Winter Games 2026 ===" . PHP_EOL;
echo "Athletes: " . count($times) . PHP_EOL;
echo "Winner : " . $results[0]["name"] . " (" . $results[0]["time"] . "s)" . PHP_EOL;
echo "Mean   : " . round(Stat::mean($times), 2) . "s" . PHP_EOL;
echo "Median : " . round(Stat::median($times), 2) . "s" . PHP_EOL;

// ---------------------------------------------------------------
// 1.  Density profile — where do finishing times cluster?
// ---------------------------------------------------------------
echo PHP_EOL . "=== 1. Density profile ===" . PHP_EOL . PHP_EOL;

// A bandwidth of 0.8s is a good fit: wide enough to smooth out
// individual gaps, narrow enough to reveal the shape of the
// distribution.  With 34 athletes, a smaller h would produce
// spiky noise; a larger h would wash out the interesting
// right-skewed tail.
$h = 0.8;
$f = Stat::kde($times, h: $h, kernel: KdeKernel::Normal);

echo "Bandwidth h = {$h}s" . PHP_EOL . PHP_EOL;

// Scan for the peak (mode of the continuous distribution)
$peakX = 0.0;
$peakD = 0.0;
$maxBarWidth = 50;
$densities = [];

for ($x = 110.0; $x <= 126.0; $x += 0.2) {
    $d = $f($x);
    $densities[] = [$x, $d];
    if ($d > $peakD) {
        $peakD = $d;
        $peakX = $x;
    }
}

echo "Density plot (each * ~ "
    . round($peakD / $maxBarWidth, 5)
    . " density units):" . PHP_EOL . PHP_EOL;

foreach ($densities as [$x, $d]) {
    // Only print every 0.6s to keep it readable
    if (round(($x - 110.0) * 10) % 6 !== 0) {
        continue;
    }
    $barLen = (int) round($d / $peakD * $maxBarWidth);
    echo str_pad(number_format($x, 1) . "s", 8)
        . "|"
        . str_repeat("*", $barLen)
        . PHP_EOL;
}

echo PHP_EOL;
echo "Peak density at "
    . number_format($peakX, 1) . "s"
    . " — this is the KDE mode, the most likely finishing time."
    . PHP_EOL;
echo "Compare with the arithmetic mean ("
    . round(Stat::mean($times), 2)
    . "s): the mean is pulled right" . PHP_EOL;
echo "by slow outliers, but KDE reveals the true concentration point."
    . PHP_EOL;

// ---------------------------------------------------------------
// 2.  Probability thresholds via CDF
// ---------------------------------------------------------------
echo PHP_EOL . "=== 2. Probability thresholds (CDF) ===" . PHP_EOL . PHP_EOL;

$F = Stat::kde($times, h: $h, cumulative: true);

$thresholds = [
    [112.0, "podium contender"],
    [113.0, "top-10 territory"],
    [113.5, "solid mid-pack"],
    [114.0, "~top 20"],
    [115.0, "lower pack"],
    [117.0, "off the pace"],
    [120.0, "struggling finisher"],
];

echo str_pad("Threshold", 12)
    . str_pad("P(time <= t)", 15)
    . "Interpretation" . PHP_EOL;
echo str_repeat("-", 65) . PHP_EOL;

foreach ($thresholds as [$t, $label]) {
    $prob = $F($t);
    echo str_pad(number_format($t, 1) . "s", 12)
        . str_pad(round($prob * 100, 1) . "%", 15)
        . $label
        . PHP_EOL;
}

// ---------------------------------------------------------------
// 3.  Classifying each athlete by density region
// ---------------------------------------------------------------
echo PHP_EOL . "=== 3. Athlete classification by density ===" . PHP_EOL . PHP_EOL;

// Use the CDF to assign a percentile to each athlete.
// KDE percentiles reflect the actual shape of the distribution,
// unlike assuming a normal distribution.
echo str_pad("Rank", 5)
    . str_pad("Athlete", 30)
    . str_pad("Time", 9)
    . str_pad("Pctile", 9)
    . "Tier" . PHP_EOL;
echo str_repeat("-", 65) . PHP_EOL;

foreach ($results as $rank => $r) {
    $pctile = $F($r["time"]) * 100;

    if ($pctile <= 15) {
        $tier = "Elite";
    } elseif ($pctile <= 40) {
        $tier = "Strong";
    } elseif ($pctile <= 70) {
        $tier = "Mid-pack";
    } elseif ($pctile <= 90) {
        $tier = "Back";
    } else {
        $tier = "Outlier";
    }

    echo str_pad((string) ($rank + 1), 5)
        . str_pad($r["name"], 30)
        . str_pad(number_format($r["time"], 2) . "s", 9)
        . str_pad(round($pctile, 1) . "%", 9)
        . $tier
        . PHP_EOL;
}

// ---------------------------------------------------------------
// 4.  Comparing kernels — does the choice matter here?
// ---------------------------------------------------------------
echo PHP_EOL . "=== 4. Kernel comparison ===" . PHP_EOL . PHP_EOL;

$kernels = [
    KdeKernel::Normal,
    KdeKernel::Triangular,
    KdeKernel::Parabolic,
    KdeKernel::Cosine,
];

$evalPoints = [112.0, 113.5, 115.0, 120.0];

echo str_pad("Kernel", 14);
foreach ($evalPoints as $ep) {
    echo str_pad(number_format($ep, 1) . "s", 10);
}
echo PHP_EOL . str_repeat("-", 54) . PHP_EOL;

foreach ($kernels as $kernel) {
    $fk = Stat::kde($times, $h, $kernel);
    echo str_pad($kernel->value, 14);
    foreach ($evalPoints as $ep) {
        echo str_pad(number_format($fk($ep), 5), 10);
    }
    echo PHP_EOL;
}

echo PHP_EOL
    . "With enough data (34 athletes) the kernel choice has minimal"
    . PHP_EOL
    . "impact — the bandwidth h matters far more."
    . PHP_EOL;

// ---------------------------------------------------------------
// 5.  Simulating future races with kdeRandom()
// ---------------------------------------------------------------
echo PHP_EOL . "=== 5. Simulating future races with kdeRandom() ===" . PHP_EOL . PHP_EOL;

// kdeRandom() draws random values from the estimated density.
// This is useful for "what-if" analysis: if the same field raced
// again under similar conditions, what might the results look like?

$nRaces = 10000;
$raceSize = count($times);
$rand = Stat::kdeRandom($times, h: $h, seed: 2026);

echo "Simulating $nRaces races of $raceSize athletes..." . PHP_EOL . PHP_EOL;

$winningTimes = [];
$podiumCuts = [];
for ($race = 0; $race < $nRaces; $race++) {
    $simTimes = [];
    for ($a = 0; $a < $raceSize; $a++) {
        $simTimes[] = $rand();
    }
    sort($simTimes);
    $winningTimes[] = $simTimes[0];
    $podiumCuts[] = $simTimes[2]; // 3rd place
}

sort($winningTimes);
sort($podiumCuts);

echo "Winning time distribution (from $nRaces simulations):" . PHP_EOL;
echo "  Fastest simulated winner : " . round(min($winningTimes), 2) . "s" . PHP_EOL;
echo "  Median winning time      : " . round(Stat::median($winningTimes), 2) . "s" . PHP_EOL;
echo "  Slowest simulated winner : " . round(max($winningTimes), 2) . "s" . PHP_EOL;
echo "  Actual winner            : " . $results[0]["time"] . "s ("
    . $results[0]["name"] . ")" . PHP_EOL;

echo PHP_EOL . "Podium threshold (3rd-place time):" . PHP_EOL;
echo "  Median podium cut-off    : " . round(Stat::median($podiumCuts), 2) . "s" . PHP_EOL;
echo "  Actual 3rd place         : " . $results[2]["time"] . "s ("
    . $results[2]["name"] . ")" . PHP_EOL;

// ---------------------------------------------------------------
// 6.  Podium probability per athlete
// ---------------------------------------------------------------
echo PHP_EOL . "=== 6. Podium probability per athlete ===" . PHP_EOL . PHP_EOL;

// For each athlete, we simulate many individual runs drawn from
// a personal KDE centered on their actual time.  We then count
// how often each athlete's simulated time would beat the simulated
// podium cut-off.
//
// This captures two sources of uncertainty:
// - race-to-race variation across the whole field (podium cut-off)
// - each athlete's own run-to-run variation

$nSim = 50000;
$personalH = 0.5; // personal run-to-run variation (narrower than field)

echo "Estimating podium probability ($nSim simulations per athlete)..." . PHP_EOL;
echo "Personal bandwidth h = {$personalH}s" . PHP_EOL . PHP_EOL;

// Pre-sort podium cuts for percentile lookup
sort($podiumCuts);
$nPodium = count($podiumCuts);

echo str_pad("Athlete", 30)
    . str_pad("Actual", 9)
    . "P(podium)" . PHP_EOL;
echo str_repeat("-", 52) . PHP_EOL;

// Show top-15 athletes (the realistic podium contenders)
for ($idx = 0; $idx < min(15, count($results)); $idx++) {
    $r = $results[$idx];
    $athleteSampler = Stat::kdeRandom([$r["time"]], h: $personalH, seed: $idx);
    $podiumCount = 0;
    for ($s = 0; $s < $nSim; $s++) {
        $simTime = $athleteSampler();
        // Compare against a random podium cut-off from our race simulations
        $cutIdx = $s % $nPodium;
        if ($simTime <= $podiumCuts[$cutIdx]) {
            $podiumCount++;
        }
    }
    $prob = $podiumCount / $nSim * 100;
    echo str_pad($r["name"], 30)
        . str_pad(number_format($r["time"], 2) . "s", 9)
        . round($prob, 1) . "%"
        . PHP_EOL;
}

echo PHP_EOL
    . "These probabilities reflect both the athlete's expected pace" . PHP_EOL
    . "and the random variation inherent in downhill racing." . PHP_EOL;
