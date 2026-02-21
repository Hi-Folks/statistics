<?php

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\Enums\KdeKernel;
use HiFolks\Statistics\Stat;

/**
 * Kernel Density Estimation (KDE) examples.
 *
 * KDE builds a smooth, continuous probability density function from
 * discrete sample data.  Think of it as a "smoothed histogram" that
 * lets you estimate the likelihood of any value — not just the ones
 * you observed.
 *
 * Inspired by the Python statistics module:
 * https://docs.python.org/3/library/statistics.html#statistics.kde
 */

// ---------------------------------------------------------------
// 1.  Basic PDF estimation (Wikipedia example)
// ---------------------------------------------------------------
echo "=== 1. Basic PDF estimation ===" . PHP_EOL . PHP_EOL;

$sample = [-2.1, -1.3, -0.4, 1.9, 5.1, 6.2];
$h = 1.5;

$f = Stat::kde($sample, h: $h);

// Evaluate the estimated density at a few points
$points = [-4.0, -2.0, 0.0, 2.0, 4.0, 6.0, 8.0];
echo "Sample : " . implode(", ", $sample) . PHP_EOL;
echo "Bandwidth h = $h" . PHP_EOL . PHP_EOL;

echo str_pad("x", 8) . "f(x)" . PHP_EOL;
echo str_repeat("-", 24) . PHP_EOL;
foreach ($points as $x) {
    $density = $f($x);
    echo str_pad(number_format($x, 1), 8) . number_format($density, 6) . PHP_EOL;
}

// ---------------------------------------------------------------
// 2.  ASCII density plot
// ---------------------------------------------------------------
echo PHP_EOL . "=== 2. ASCII density plot ===" . PHP_EOL . PHP_EOL;

$xMin = -6.0;
$xMax = 10.0;
$steps = 60;
$maxBarWidth = 50;

// Compute densities across the range
$densities = [];
$maxDensity = 0.0;
for ($i = 0; $i <= $steps; $i++) {
    $x = $xMin + ($xMax - $xMin) * $i / $steps;
    $d = $f($x);
    $densities[] = [$x, $d];
    if ($d > $maxDensity) {
        $maxDensity = $d;
    }
}

foreach ($densities as [$x, $d]) {
    $barLen = (int) round($d / $maxDensity * $maxBarWidth);
    echo str_pad(number_format($x, 1), 7)
        . " |"
        . str_repeat("*", $barLen)
        . PHP_EOL;
}

// ---------------------------------------------------------------
// 3.  Comparing kernels
// ---------------------------------------------------------------
echo PHP_EOL . "=== 3. Comparing kernels ===" . PHP_EOL . PHP_EOL;

$data = [1.0, 2.0, 3.0, 4.0, 5.0];
$evalAt = 3.0;

$kernelsToCompare = [
    KdeKernel::Normal,
    KdeKernel::Triangular,
    KdeKernel::Rectangular,
    KdeKernel::Parabolic,
    KdeKernel::Cosine,
];

echo "Data: " . implode(", ", $data) . PHP_EOL;
echo "Evaluating density at x = $evalAt  (h = 1.0)" . PHP_EOL . PHP_EOL;

echo str_pad("Kernel", 16) . "f($evalAt)" . PHP_EOL;
echo str_repeat("-", 30) . PHP_EOL;
foreach ($kernelsToCompare as $kernel) {
    $fk = Stat::kde($data, 1.0, $kernel);
    echo str_pad($kernel->value, 16)
        . number_format($fk($evalAt), 6)
        . PHP_EOL;
}

// ---------------------------------------------------------------
// 4.  Cumulative Distribution Function (CDF)
// ---------------------------------------------------------------
echo PHP_EOL . "=== 4. Cumulative Distribution Function ===" . PHP_EOL . PHP_EOL;

$F = Stat::kde($sample, h: $h, cumulative: true);

echo "Sample : " . implode(", ", $sample) . PHP_EOL;
echo "Bandwidth h = $h" . PHP_EOL . PHP_EOL;

echo str_pad("x", 8) . "F(x)" . PHP_EOL;
echo str_repeat("-", 24) . PHP_EOL;
foreach ([-6.0, -4.0, -2.0, 0.0, 2.0, 4.0, 6.0, 8.0, 10.0] as $x) {
    echo str_pad(number_format($x, 1), 8)
        . number_format($F($x), 6)
        . PHP_EOL;
}

// P(X <= 2.5)
$p = $F(2.5);
echo PHP_EOL . "P(X <= 2.5) = " . round($p * 100, 1) . "%" . PHP_EOL;

// ---------------------------------------------------------------
// 5.  Alias equivalence
// ---------------------------------------------------------------
echo PHP_EOL . "=== 5. Alias equivalence ===" . PHP_EOL . PHP_EOL;

$aliasPairs = [
    [KdeKernel::Gauss, KdeKernel::Normal],
    [KdeKernel::Uniform, KdeKernel::Rectangular],
    [KdeKernel::Epanechnikov, KdeKernel::Parabolic],
    [KdeKernel::Biweight, KdeKernel::Quartic],
];

echo "Aliases resolve to their canonical kernel:" . PHP_EOL;
foreach ($aliasPairs as [$alias, $canonical]) {
    $f1 = Stat::kde($data, 1.0, $alias);
    $f2 = Stat::kde($data, 1.0, $canonical);
    $match = abs($f1(3.0) - $f2(3.0)) < 1e-15 ? "OK" : "MISMATCH";
    echo "  " . str_pad($alias->value, 14) . " => "
        . str_pad($canonical->value, 14)
        . $match . PHP_EOL;
}

// ---------------------------------------------------------------
// 6.  Random sampling with kdeRandom()
// ---------------------------------------------------------------
echo PHP_EOL . "=== 6. Random sampling with kdeRandom() ===" . PHP_EOL . PHP_EOL;

$rand = Stat::kdeRandom($sample, h: $h, seed: 8675309);

$nSamples = 10;
$samples = [];
for ($i = 0; $i < $nSamples; $i++) {
    $samples[] = round($rand(), 1);
}
echo "Original data : " . implode(", ", $sample) . PHP_EOL;
echo "10 KDE samples: " . implode(", ", $samples) . PHP_EOL;

// ---------------------------------------------------------------
// 7.  Verifying statistical properties of random samples
// ---------------------------------------------------------------
echo PHP_EOL . "=== 7. Statistical properties of KDE samples ===" . PHP_EOL . PHP_EOL;

$dataMean = Stat::mean($sample);
$n = 50000;
$sampler = Stat::kdeRandom($sample, h: $h, seed: 42);

$sum = 0.0;
for ($i = 0; $i < $n; $i++) {
    $sum += $sampler();
}
$sampleMean = $sum / $n;

echo "Original data mean : " . round($dataMean, 4) . PHP_EOL;
echo "KDE sample mean (n=$n): " . round($sampleMean, 4) . PHP_EOL;
echo "Difference           : " . round(abs($dataMean - $sampleMean), 4) . PHP_EOL;

// ---------------------------------------------------------------
// 8.  Sampling with different kernels
// ---------------------------------------------------------------
echo PHP_EOL . "=== 8. Sampling with different kernels ===" . PHP_EOL . PHP_EOL;

echo "5 random draws per kernel (seed=42):" . PHP_EOL . PHP_EOL;
foreach ($kernelsToCompare as $kernel) {
    $sampler = Stat::kdeRandom($sample, h: $h, kernel: $kernel, seed: 42);
    $draws = [];
    for ($i = 0; $i < 5; $i++) {
        $draws[] = round($sampler(), 2);
    }
    echo str_pad($kernel->value, 16) . implode(", ", $draws) . PHP_EOL;
}
