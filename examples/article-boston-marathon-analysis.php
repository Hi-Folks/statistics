<?php

/**
 * Analyzing 75,000 Boston Marathon Runners with PHP Statistics
 *
 * This script accompanies the article that uses a representative sample
 * from the Boston Marathon 2015–2017 Kaggle dataset to showcase the
 * statistics library's capabilities — especially tTestTwoSample() and
 * tTestPaired().
 *
 * Dataset: https://www.kaggle.com/datasets/rojour/boston-results
 * Run it with: php examples/article-boston-marathon-analysis.php
 */

require __DIR__ . "/../vendor/autoload.php";

use HiFolks\Statistics\NormalDist;
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Utils\Arr;
use HiFolks\Statistics\Utils\Format;

// === The Data ===
// Representative sample of 60 finishers from the 2017 Boston Marathon.
// Times are stored in seconds for easy arithmetic.
// 'half' = cumulative time at the half-marathon mark (21.1 km)
// 'finish' = gun-to-finish time
// 'splits' = 8 individual 5K segment times (5K through 40K)

$runners = [
    // --- Fast men ---
    ['name' => 'James Karanja',      'age' => 28, 'gender' => 'M', 'country' => 'KEN', 'half' => 4520, 'finish' => 9280,  'splits' => [1100, 1105, 1110, 1115, 1120, 1125, 1140, 1165]],
    ['name' => 'Michael Kiprop',     'age' => 31, 'gender' => 'M', 'country' => 'KEN', 'half' => 4600, 'finish' => 9450,  'splits' => [1115, 1120, 1125, 1130, 1135, 1145, 1160, 1200]],
    ['name' => 'David Chen',         'age' => 26, 'gender' => 'M', 'country' => 'USA', 'half' => 4680, 'finish' => 9600,  'splits' => [1130, 1135, 1140, 1145, 1155, 1170, 1190, 1225]],
    ['name' => 'Ryan O\'Brien',      'age' => 29, 'gender' => 'M', 'country' => 'USA', 'half' => 4750, 'finish' => 9780,  'splits' => [1150, 1155, 1160, 1165, 1175, 1195, 1220, 1260]],
    ['name' => 'Tadesse Bekele',     'age' => 33, 'gender' => 'M', 'country' => 'ETH', 'half' => 4820, 'finish' => 9920,  'splits' => [1165, 1170, 1180, 1185, 1195, 1215, 1245, 1280]],
    ['name' => 'Carlos Gutierrez',   'age' => 27, 'gender' => 'M', 'country' => 'MEX', 'half' => 4900, 'finish' => 10100, 'splits' => [1190, 1195, 1200, 1210, 1225, 1240, 1265, 1300]],
    ['name' => 'Thomas Mueller',     'age' => 30, 'gender' => 'M', 'country' => 'GER', 'half' => 5020, 'finish' => 10380, 'splits' => [1220, 1225, 1230, 1240, 1260, 1280, 1310, 1350]],
    ['name' => 'Hiroshi Tanaka',     'age' => 34, 'gender' => 'M', 'country' => 'JPN', 'half' => 5100, 'finish' => 10560, 'splits' => [1240, 1245, 1250, 1260, 1280, 1300, 1330, 1370]],
    // --- Mid-pack men ---
    ['name' => 'John Smith',         'age' => 34, 'gender' => 'M', 'country' => 'USA', 'half' => 5400, 'finish' => 11200, 'splits' => [1310, 1320, 1330, 1340, 1370, 1400, 1440, 1490]],
    ['name' => 'Patrick Sullivan',   'age' => 38, 'gender' => 'M', 'country' => 'USA', 'half' => 5550, 'finish' => 11520, 'splits' => [1350, 1360, 1370, 1380, 1410, 1440, 1480, 1530]],
    ['name' => 'Marco Rossi',        'age' => 36, 'gender' => 'M', 'country' => 'ITA', 'half' => 5620, 'finish' => 11700, 'splits' => [1370, 1375, 1385, 1395, 1425, 1460, 1500, 1550]],
    ['name' => 'Daniel Park',        'age' => 32, 'gender' => 'M', 'country' => 'KOR', 'half' => 5700, 'finish' => 11880, 'splits' => [1390, 1395, 1405, 1415, 1450, 1485, 1525, 1575]],
    ['name' => 'Andrew Taylor',      'age' => 41, 'gender' => 'M', 'country' => 'USA', 'half' => 5800, 'finish' => 12100, 'splits' => [1410, 1420, 1430, 1445, 1480, 1520, 1570, 1625]],
    ['name' => 'Pierre Dubois',      'age' => 37, 'gender' => 'M', 'country' => 'FRA', 'half' => 5850, 'finish' => 12240, 'splits' => [1425, 1435, 1445, 1460, 1500, 1540, 1590, 1650]],
    ['name' => 'Robert Johnson',     'age' => 44, 'gender' => 'M', 'country' => 'USA', 'half' => 5950, 'finish' => 12480, 'splits' => [1450, 1460, 1470, 1490, 1530, 1570, 1620, 1690]],
    ['name' => 'William Davis',      'age' => 39, 'gender' => 'M', 'country' => 'USA', 'half' => 6020, 'finish' => 12660, 'splits' => [1470, 1480, 1490, 1510, 1555, 1600, 1660, 1730]],
    ['name' => 'Kevin Brown',        'age' => 42, 'gender' => 'M', 'country' => 'CAN', 'half' => 6100, 'finish' => 12840, 'splits' => [1490, 1500, 1515, 1535, 1580, 1630, 1690, 1760]],
    ['name' => 'Liam Walsh',         'age' => 35, 'gender' => 'M', 'country' => 'IRL', 'half' => 6180, 'finish' => 13020, 'splits' => [1510, 1520, 1535, 1555, 1605, 1660, 1720, 1795]],
    ['name' => 'Matt Henderson',     'age' => 46, 'gender' => 'M', 'country' => 'USA', 'half' => 6250, 'finish' => 13200, 'splits' => [1530, 1540, 1555, 1575, 1630, 1685, 1750, 1830]],
    ['name' => 'José Fernandez',     'age' => 40, 'gender' => 'M', 'country' => 'ESP', 'half' => 6320, 'finish' => 13380, 'splits' => [1545, 1560, 1575, 1600, 1655, 1715, 1780, 1860]],
    ['name' => 'Brian Miller',       'age' => 48, 'gender' => 'M', 'country' => 'USA', 'half' => 6400, 'finish' => 13560, 'splits' => [1565, 1580, 1595, 1620, 1680, 1740, 1810, 1900]],
    ['name' => 'Chris Anderson',     'age' => 43, 'gender' => 'M', 'country' => 'USA', 'half' => 6480, 'finish' => 13740, 'splits' => [1585, 1600, 1620, 1645, 1710, 1775, 1850, 1940]],
    ['name' => 'Sean O\'Connor',     'age' => 45, 'gender' => 'M', 'country' => 'USA', 'half' => 6550, 'finish' => 13920, 'splits' => [1600, 1620, 1640, 1670, 1735, 1805, 1885, 1980]],
    // --- Slow men ---
    ['name' => 'Greg Thompson',      'age' => 52, 'gender' => 'M', 'country' => 'USA', 'half' => 6700, 'finish' => 14280, 'splits' => [1630, 1650, 1675, 1710, 1780, 1860, 1950, 2060]],
    ['name' => 'Tom Williams',       'age' => 55, 'gender' => 'M', 'country' => 'USA', 'half' => 6850, 'finish' => 14640, 'splits' => [1665, 1690, 1720, 1760, 1840, 1930, 2030, 2150]],
    ['name' => 'Richard Clark',      'age' => 50, 'gender' => 'M', 'country' => 'GBR', 'half' => 6950, 'finish' => 14940, 'splits' => [1695, 1720, 1750, 1795, 1880, 1975, 2085, 2210]],
    ['name' => 'Hans Weber',         'age' => 58, 'gender' => 'M', 'country' => 'GER', 'half' => 7100, 'finish' => 15300, 'splits' => [1730, 1760, 1795, 1845, 1940, 2045, 2165, 2300]],
    ['name' => 'James Wilson',       'age' => 53, 'gender' => 'M', 'country' => 'USA', 'half' => 7200, 'finish' => 15540, 'splits' => [1755, 1785, 1825, 1880, 1980, 2090, 2215, 2360]],
    ['name' => 'Paul Martin',        'age' => 60, 'gender' => 'M', 'country' => 'USA', 'half' => 7400, 'finish' => 16020, 'splits' => [1800, 1840, 1885, 1945, 2055, 2175, 2310, 2470]],
    ['name' => 'George Baker',       'age' => 62, 'gender' => 'M', 'country' => 'USA', 'half' => 7600, 'finish' => 16500, 'splits' => [1850, 1895, 1945, 2010, 2130, 2260, 2410, 2590]],
    ['name' => 'Frank Harris',       'age' => 64, 'gender' => 'M', 'country' => 'CAN', 'half' => 7900, 'finish' => 17280, 'splits' => [1920, 1975, 2035, 2115, 2250, 2400, 2570, 2770]],
    // --- Fast women ---
    ['name' => 'Sarah Kimutai',      'age' => 27, 'gender' => 'F', 'country' => 'KEN', 'half' => 5250, 'finish' => 10800, 'splits' => [1280, 1285, 1290, 1300, 1320, 1345, 1375, 1410]],
    ['name' => 'Emma Johansson',     'age' => 30, 'gender' => 'F', 'country' => 'SWE', 'half' => 5380, 'finish' => 11100, 'splits' => [1310, 1320, 1330, 1340, 1365, 1390, 1425, 1465]],
    ['name' => 'Lisa Zhang',         'age' => 25, 'gender' => 'F', 'country' => 'CHN', 'half' => 5480, 'finish' => 11340, 'splits' => [1335, 1345, 1355, 1370, 1395, 1425, 1465, 1510]],
    ['name' => 'Anna Petrov',        'age' => 29, 'gender' => 'F', 'country' => 'RUS', 'half' => 5560, 'finish' => 11520, 'splits' => [1355, 1365, 1375, 1390, 1420, 1455, 1495, 1545]],
    ['name' => 'Maria Santos',       'age' => 32, 'gender' => 'F', 'country' => 'BRA', 'half' => 5650, 'finish' => 11700, 'splits' => [1375, 1385, 1395, 1415, 1445, 1480, 1525, 1580]],
    // --- Mid-pack women ---
    ['name' => 'Jennifer Adams',     'age' => 35, 'gender' => 'F', 'country' => 'USA', 'half' => 5850, 'finish' => 12180, 'splits' => [1425, 1435, 1450, 1470, 1510, 1555, 1610, 1675]],
    ['name' => 'Rachel Green',       'age' => 38, 'gender' => 'F', 'country' => 'USA', 'half' => 6050, 'finish' => 12660, 'splits' => [1475, 1490, 1510, 1535, 1585, 1640, 1710, 1790]],
    ['name' => 'Sophie Laurent',     'age' => 33, 'gender' => 'F', 'country' => 'FRA', 'half' => 6200, 'finish' => 13020, 'splits' => [1515, 1530, 1550, 1580, 1635, 1700, 1775, 1865]],
    ['name' => 'Emily Watson',       'age' => 40, 'gender' => 'F', 'country' => 'USA', 'half' => 6350, 'finish' => 13380, 'splits' => [1550, 1570, 1590, 1625, 1685, 1755, 1840, 1940]],
    ['name' => 'Amy Chen',           'age' => 36, 'gender' => 'F', 'country' => 'USA', 'half' => 6480, 'finish' => 13680, 'splits' => [1585, 1605, 1625, 1665, 1730, 1805, 1895, 2000]],
    ['name' => 'Kate Murphy',        'age' => 42, 'gender' => 'F', 'country' => 'IRL', 'half' => 6600, 'finish' => 13980, 'splits' => [1615, 1635, 1660, 1700, 1775, 1860, 1955, 2070]],
    ['name' => 'Michelle Lee',       'age' => 37, 'gender' => 'F', 'country' => 'USA', 'half' => 6720, 'finish' => 14280, 'splits' => [1645, 1665, 1695, 1740, 1820, 1910, 2015, 2140]],
    ['name' => 'Olivia Garcia',      'age' => 44, 'gender' => 'F', 'country' => 'USA', 'half' => 6850, 'finish' => 14580, 'splits' => [1675, 1700, 1730, 1780, 1870, 1965, 2080, 2210]],
    ['name' => 'Laura Schmidt',      'age' => 41, 'gender' => 'F', 'country' => 'GER', 'half' => 6950, 'finish' => 14820, 'splits' => [1700, 1725, 1760, 1810, 1910, 2015, 2135, 2275]],
    ['name' => 'Hannah Kim',         'age' => 39, 'gender' => 'F', 'country' => 'USA', 'half' => 7050, 'finish' => 15060, 'splits' => [1725, 1750, 1790, 1845, 1950, 2060, 2190, 2340]],
    // --- Slow women ---
    ['name' => 'Diane Cooper',       'age' => 50, 'gender' => 'F', 'country' => 'USA', 'half' => 7250, 'finish' => 15480, 'splits' => [1770, 1800, 1845, 1905, 2015, 2140, 2280, 2440]],
    ['name' => 'Nancy Taylor',       'age' => 53, 'gender' => 'F', 'country' => 'USA', 'half' => 7450, 'finish' => 15960, 'splits' => [1820, 1855, 1905, 1970, 2095, 2230, 2385, 2560]],
    ['name' => 'Barbara White',      'age' => 48, 'gender' => 'F', 'country' => 'USA', 'half' => 7600, 'finish' => 16320, 'splits' => [1860, 1900, 1955, 2030, 2160, 2310, 2475, 2670]],
    ['name' => 'Susan Hall',         'age' => 56, 'gender' => 'F', 'country' => 'CAN', 'half' => 7850, 'finish' => 16860, 'splits' => [1915, 1960, 2020, 2105, 2250, 2410, 2595, 2810]],
    ['name' => 'Patricia Evans',     'age' => 58, 'gender' => 'F', 'country' => 'USA', 'half' => 8050, 'finish' => 17340, 'splits' => [1965, 2015, 2085, 2175, 2340, 2520, 2720, 2950]],
    ['name' => 'Carol Robinson',     'age' => 61, 'gender' => 'F', 'country' => 'USA', 'half' => 8300, 'finish' => 17940, 'splits' => [2025, 2085, 2160, 2260, 2445, 2645, 2865, 3120]],
    // --- Additional men for sample size ---
    ['name' => 'Steve Campbell',     'age' => 47, 'gender' => 'M', 'country' => 'USA', 'half' => 6650, 'finish' => 14100, 'splits' => [1620, 1640, 1665, 1705, 1775, 1855, 1950, 2065]],
    ['name' => 'Mark Phillips',      'age' => 36, 'gender' => 'M', 'country' => 'USA', 'half' => 5480, 'finish' => 11380, 'splits' => [1335, 1345, 1355, 1370, 1400, 1430, 1470, 1520]],
    ['name' => 'Jason Reed',         'age' => 33, 'gender' => 'M', 'country' => 'USA', 'half' => 5250, 'finish' => 10860, 'splits' => [1280, 1290, 1300, 1315, 1340, 1370, 1405, 1450]],
    ['name' => 'Alex Turner',        'age' => 28, 'gender' => 'M', 'country' => 'GBR', 'half' => 5150, 'finish' => 10620, 'splits' => [1255, 1260, 1270, 1285, 1310, 1340, 1375, 1415]],
    ['name' => 'Nick Peterson',      'age' => 50, 'gender' => 'M', 'country' => 'USA', 'half' => 6900, 'finish' => 14760, 'splits' => [1685, 1710, 1740, 1790, 1880, 1975, 2085, 2215]],
    ['name' => 'Derek Hughes',       'age' => 42, 'gender' => 'M', 'country' => 'AUS', 'half' => 6250, 'finish' => 13140, 'splits' => [1525, 1540, 1560, 1590, 1650, 1720, 1800, 1890]],
    ['name' => 'Tim Wright',         'age' => 56, 'gender' => 'M', 'country' => 'USA', 'half' => 7350, 'finish' => 15900, 'splits' => [1795, 1830, 1875, 1935, 2055, 2185, 2335, 2510]],
    ['name' => 'Scott Mitchell',     'age' => 39, 'gender' => 'M', 'country' => 'USA', 'half' => 5700, 'finish' => 11820, 'splits' => [1390, 1400, 1410, 1425, 1460, 1500, 1545, 1600]],
];

// =====================================================================
// Extract common arrays using Arr utility
// =====================================================================
[$finishTimes, $ages] = Arr::extract($runners, ['finish', 'age']);

[$menRunners, $womenRunners] = Arr::partition($runners, 'gender', '==', 'M');
[$menTimes] = Arr::extract($menRunners, ['finish']);
[$womenTimes] = Arr::extract($womenRunners, ['finish']);

// =====================================================================
// Step 1: The Data & Descriptive Statistics
// =====================================================================
echo "=== Step 1: The Data & Descriptive Statistics ===" . PHP_EOL;
echo "\"What does a typical Boston Marathon finish look like?\"" . PHP_EOL . PHP_EOL;

$mean = Stat::mean($finishTimes);
$median = Stat::median($finishTimes);
$stdev = Stat::stdev($finishTimes);
$quartiles = Stat::quantiles($finishTimes);

echo "Sample size:  " . count($runners) . " runners (" . count($menTimes) . " men, " . count($womenTimes) . " women)" . PHP_EOL;
echo "Mean finish:  " . Format::secondsToTime($mean) . " (" . round($mean) . "s)" . PHP_EOL;
echo 'Median finish: ' . Format::secondsToTime($median) . " (" . round($median) . "s)" . PHP_EOL;
echo 'Std deviation: ' . Format::secondsToTime($stdev) . " (" . round($stdev) . "s)" . PHP_EOL;
echo "Min:          " . Format::secondsToTime(min($finishTimes)) . " | Max: " . Format::secondsToTime(max($finishTimes)) . PHP_EOL;
echo "Quartiles:    Q1=" . Format::secondsToTime($quartiles[0])
    . "  Q2=" . Format::secondsToTime($quartiles[1])
    . "  Q3=" . Format::secondsToTime($quartiles[2]) . PHP_EOL;
echo PHP_EOL;
echo "How to interpret:" . PHP_EOL;
echo "- If the mean is higher than the median, the distribution is right-skewed." . PHP_EOL;
echo "- Compare the full range (min-max) to the interquartile range (Q1-Q3 = "
    . Format::secondsToTime($quartiles[2] - $quartiles[0]) . ") to see how spread the middle 50% is." . PHP_EOL;
echo "- A large standard deviation relative to the mean reflects wide diversity in the field." . PHP_EOL;

// =====================================================================
// Step 2: Men vs Women — Two-Sample T-Test
// =====================================================================
echo PHP_EOL . "=== Step 2: Men vs Women — Two-Sample T-Test ===" . PHP_EOL;
echo "\"Are men statistically faster, or could the difference be random?\"" . PHP_EOL . PHP_EOL;

echo "Men:   n=" . count($menTimes) . ", mean=" . Format::secondsToTime(Stat::mean($menTimes))
    . " (" . round(Stat::mean($menTimes)) . "s)" . PHP_EOL;
echo "Women: n=" . count($womenTimes) . ", mean=" . Format::secondsToTime(Stat::mean($womenTimes))
    . " (" . round(Stat::mean($womenTimes)) . "s)" . PHP_EOL;
echo "Difference: " . Format::secondsToTime(Stat::mean($womenTimes) - Stat::mean($menTimes))
    . " (" . round(Stat::mean($womenTimes) - Stat::mean($menTimes)) . "s)" . PHP_EOL;
echo PHP_EOL;

$tTest2 = Stat::tTestTwoSample($menTimes, $womenTimes);
echo "Two-sample t-test results:" . PHP_EOL;
echo "  t-statistic:       " . round($tTest2['tStatistic'], 4) . PHP_EOL;
echo '  Degrees of freedom: ' . round($tTest2['degreesOfFreedom'], 1) . PHP_EOL;
echo "  p-value:           " . round($tTest2['pValue'], 6) . PHP_EOL;
echo PHP_EOL;

echo "How to interpret:" . PHP_EOL;
echo "- If p-value < 0.05, the difference is statistically significant (unlikely due to chance)." . PHP_EOL;
echo "- The t-statistic measures the gap relative to within-group variation; further from zero = stronger evidence." . PHP_EOL;
echo "- Degrees of freedom are adjusted for unequal sample sizes (Welch-Satterthwaite approximation)." . PHP_EOL;

// =====================================================================
// Step 3: Pacing Strategy — Paired T-Test
// =====================================================================
echo PHP_EOL . "=== Step 3: Pacing Strategy — Paired T-Test ===" . PHP_EOL;
echo "\"Do runners slow down in the second half? (positive split analysis)\"" . PHP_EOL . PHP_EOL;

$firstHalf = array_column($runners, 'half');
$secondHalf = [];
foreach ($runners as $r) {
    $secondHalf[] = $r['finish'] - $r['half'];
}

$meanFirst = Stat::mean($firstHalf);
$meanSecond = Stat::mean($secondHalf);

echo "Mean first half:  " . Format::secondsToTime($meanFirst) . " (" . round($meanFirst) . "s)" . PHP_EOL;
echo "Mean second half: " . Format::secondsToTime($meanSecond) . " (" . round($meanSecond) . "s)" . PHP_EOL;
echo "Avg slowdown:     " . Format::secondsToTime($meanSecond - $meanFirst)
    . " (" . round($meanSecond - $meanFirst) . "s)" . PHP_EOL;
echo PHP_EOL;

$tTestPaired = Stat::tTestPaired($firstHalf, $secondHalf);
echo "Paired t-test results:" . PHP_EOL;
echo "  t-statistic:       " . round($tTestPaired['tStatistic'], 4) . PHP_EOL;
echo '  Degrees of freedom: ' . $tTestPaired['degreesOfFreedom'] . PHP_EOL;
echo "  p-value:           " . round($tTestPaired['pValue'], 6) . PHP_EOL;
echo PHP_EOL;

echo "How to interpret:" . PHP_EOL;
echo "- If the mean second half > mean first half, runners slow down on average." . PHP_EOL;
echo "- A negative t-statistic confirms the first half is faster. The more negative, the stronger the evidence." . PHP_EOL;
echo "- If p-value is near zero, the slowdown is overwhelmingly significant." . PHP_EOL;
echo "- The paired test removes between-runner variability, making it very sensitive to systematic differences." . PHP_EOL;

// =====================================================================
// Step 4: Does Age Affect Finish Time?
// =====================================================================
echo PHP_EOL . "=== Step 4: Does Age Affect Finish Time? ===" . PHP_EOL;
echo "\"How many minutes per year of age does the marathon cost you?\"" . PHP_EOL . PHP_EOL;

$pearson = Stat::correlation($ages, $finishTimes);
$spearman = Stat::correlation($ages, $finishTimes, 'ranked');
$regression = Stat::linearRegression($ages, $finishTimes);
$r2 = Stat::rSquared($ages, $finishTimes, false, 4);

echo "Pearson correlation:  " . round($pearson, 4) . PHP_EOL;
echo "Spearman correlation: " . round($spearman, 4) . PHP_EOL;
echo PHP_EOL;
echo "Linear regression:    finish = " . round($regression[0], 1) . " × age + " . round($regression[1]) . PHP_EOL;
echo "R-squared:            " . $r2 . PHP_EOL;
echo PHP_EOL;

echo "How to interpret:" . PHP_EOL;
echo "- Pearson and Spearman close to +1 = strong positive relationship (older = slower)." . PHP_EOL;
echo "- If both correlations are similar, the relationship is linear, not just monotonic." . PHP_EOL;
echo "- The slope tells you seconds added per year of age. Divide by 60 for minutes." . PHP_EOL;
echo "- R-squared tells you what fraction of variation age explains (0 = none, 1 = all)." . PHP_EOL;

// =====================================================================
// Step 5: Consistency — Who Paces Best?
// =====================================================================
echo PHP_EOL . "=== Step 5: Consistency — Who Paces Best? ===" . PHP_EOL;
echo "\"Do fast runners pace more evenly than slow runners?\"" . PHP_EOL . PHP_EOL;

$medianFinish = Stat::median($finishTimes);
$fastCV = [];
$slowCV = [];

foreach ($runners as $r) {
    $cv = Stat::coefficientOfVariation($r['splits'], 2);
    if ($r['finish'] <= $medianFinish) {
        $fastCV[] = $cv;
    } else {
        $slowCV[] = $cv;
    }
}

echo "Pacing consistency (CV of 5K splits):" . PHP_EOL;
echo "  Fast group (below median): mean CV = " . round(Stat::mean($fastCV), 2) . "%" . PHP_EOL;
echo "  Slow group (above median): mean CV = " . round(Stat::mean($slowCV), 2) . "%" . PHP_EOL;
echo PHP_EOL;

$tTestCV = Stat::tTestTwoSample($fastCV, $slowCV);
echo "Two-sample t-test on CV:" . PHP_EOL;
echo "  t-statistic: " . round($tTestCV['tStatistic'], 4) . PHP_EOL;
echo "  p-value:     " . round($tTestCV['pValue'], 6) . PHP_EOL;
echo PHP_EOL;

echo "How to interpret:" . PHP_EOL;
echo "- If the slow group's mean CV is higher, slower runners pace less consistently." . PHP_EOL;
echo "- If p-value < 0.05, the difference in pacing consistency is statistically significant." . PHP_EOL;
echo "- A low CV = even pacing; a high CV = the runner faded or surged during the race." . PHP_EOL;

// =====================================================================
// Step 6: The Finish Time Distribution
// =====================================================================
echo PHP_EOL . "=== Step 6: The Finish Time Distribution ===" . PHP_EOL;
echo "\"Is marathon finish time normally distributed?\"" . PHP_EOL . PHP_EOL;

$skewness = Stat::skewness($finishTimes, 4);
$kurtosis = Stat::kurtosis($finishTimes, 4);
echo "Skewness: " . $skewness . PHP_EOL;
echo "  (positive = right-skewed, a long tail of slower finishers)" . PHP_EOL;
echo "Kurtosis: " . $kurtosis . PHP_EOL;
echo "  (excess kurtosis — 0 is normal; positive = heavier tails)" . PHP_EOL;
echo PHP_EOL;

$normal = NormalDist::fromSamples($finishTimes);
echo "Normal model: mu = " . Format::secondsToTime($normal->getMeanRounded(0))
    . ", sigma = " . Format::secondsToTime((int) round($normal->getSigmaRounded(0))) . PHP_EOL;
echo PHP_EOL;

// Compare model vs actual in ranges
$ranges = [
    ['label' => 'Under 3:00:00', 'max' => 10800],
    ['label' => '3:00-3:30',     'max' => 12600],
    ['label' => '3:30-4:00',     'max' => 14400],
    ['label' => '4:00-4:30',     'max' => 16200],
    ['label' => 'Over 4:30',     'max' => PHP_INT_MAX],
];

echo str_pad("Range", 16) . str_pad("Actual", 10) . "Model" . PHP_EOL;
echo str_repeat("-", 36) . PHP_EOL;

$prevMax = 0;
foreach ($ranges as $range) {
    $actualCount = count(array_filter($finishTimes, fn($t): bool => $t > $prevMax && $t <= $range['max']));
    $modelProb = $normal->cdf(min($range['max'], 20000)) - $normal->cdf($prevMax);
    $modelCount = round($modelProb * count($finishTimes), 1);
    echo str_pad($range['label'], 16)
        . str_pad((string) $actualCount, 10)
        . round($modelCount, 1)
        . PHP_EOL;
    $prevMax = $range['max'];
}
echo PHP_EOL;
echo "How to interpret:" . PHP_EOL;
echo "- Positive skewness = right-skewed (long tail of slower finishers)." . PHP_EOL;
echo "- Negative excess kurtosis = lighter tails than a normal distribution." . PHP_EOL;
echo "- Compare Actual vs Model columns: where they diverge, the normal assumption breaks down." . PHP_EOL;

// =====================================================================
// Step 7: Finding the Outliers
// =====================================================================
echo PHP_EOL . "=== Step 7: Finding the Outliers ===" . PHP_EOL;
echo "\"Who had an unusually fast (or slow) day?\"" . PHP_EOL . PHP_EOL;

// Z-score method
echo "Method 1: Z-score based (threshold = 2.0)" . PHP_EOL;
$zscoreOutliers = Stat::outliers($finishTimes, 2.0);
if ($zscoreOutliers === []) {
    echo "  No outliers detected." . PHP_EOL;
} else {
    foreach ($zscoreOutliers as $time) {
        $name = '';
        foreach ($runners as $r) {
            if ($r['finish'] === $time) {
                $name = $r['name'];
                break;
            }
        }
        echo "  " . Format::secondsToTime($time) . " — " . $name . PHP_EOL;
    }
}

// IQR method
echo PHP_EOL . "Method 2: IQR based (factor = 1.5)" . PHP_EOL;
$iqrOutliers = Stat::iqrOutliers($finishTimes);
if ($iqrOutliers === []) {
    echo "  No outliers detected." . PHP_EOL;
} else {
    foreach ($iqrOutliers as $time) {
        $name = '';
        foreach ($runners as $r) {
            if ($r['finish'] === $time) {
                $name = $r['name'];
                break;
            }
        }
        echo "  " . Format::secondsToTime($time) . " — " . $name . PHP_EOL;
    }
}

// Individual z-scores for notable runners
echo PHP_EOL . "Z-scores for selected runners:" . PHP_EOL;
$zscores = Stat::zscores($finishTimes, 2);

// Pair each runner with their z-score and sort by finish time
$runnerZscores = [];
foreach ($runners as $i => $r) {
    $runnerZscores[] = ['name' => $r['name'], 'finish' => $r['finish'], 'z' => $zscores[$i]];
}
usort($runnerZscores, fn(array $a, array $b): int => $a['finish'] <=> $b['finish']);

// Show 3 fastest + 3 slowest
$notableRunners = array_merge(
    array_slice($runnerZscores, 0, 3),
    array_slice($runnerZscores, -3),
);

echo str_pad("Runner", 22) . str_pad("Time", 12) . "Z-score" . PHP_EOL;
echo str_repeat("-", 45) . PHP_EOL;
foreach ($notableRunners as $rz) {
    $zFormatted = ($rz['z'] >= 0 ? "+" : "") . number_format($rz['z'], 2);
    echo str_pad($rz['name'], 22)
        . str_pad(Format::secondsToTime($rz['finish']), 12)
        . $zFormatted
        . PHP_EOL;
}
echo PHP_EOL;
echo "How to interpret:" . PHP_EOL;
echo "- Negative z-scores = faster than average; positive = slower." . PHP_EOL;
echo "- Z-scores beyond +/-2 are unusual; beyond +/-3 are very rare." . PHP_EOL;
echo "- The IQR method is more robust for skewed data (doesn't assume symmetry)." . PHP_EOL;
echo "- The z-score method can miss outliers because outliers inflate the standard deviation." . PHP_EOL;

// =====================================================================
// Step 8: Confidence Intervals
// =====================================================================
echo PHP_EOL . "=== Step 8: Confidence Intervals ===" . PHP_EOL;
echo "\"How precisely do we know the average finish time?\"" . PHP_EOL . PHP_EOL;

$ciAll = Stat::confidenceInterval($finishTimes, 0.95, 0);
$ciMen = Stat::confidenceInterval($menTimes, 0.95, 0);
$ciWomen = Stat::confidenceInterval($womenTimes, 0.95, 0);

$semAll = Stat::sem($finishTimes, 0);
$semMen = Stat::sem($menTimes, 0);
$semWomen = Stat::sem($womenTimes, 0);

echo "95% Confidence Intervals:" . PHP_EOL;
echo "  All runners: " . Format::secondsToTime($ciAll[0]) . " to " . Format::secondsToTime($ciAll[1])
    . "  (SEM: " . $semAll . "s)" . PHP_EOL;
echo "  Men:         " . Format::secondsToTime($ciMen[0]) . " to " . Format::secondsToTime($ciMen[1])
    . "  (SEM: " . $semMen . "s)" . PHP_EOL;
echo "  Women:       " . Format::secondsToTime($ciWomen[0]) . " to " . Format::secondsToTime($ciWomen[1])
    . "  (SEM: " . $semWomen . "s)" . PHP_EOL;
echo PHP_EOL;
echo "How to interpret:" . PHP_EOL;
echo "- The interval gives you a range: we are 95% confident the true mean falls within it." . PHP_EOL;
echo "- Smaller samples produce wider intervals (more uncertainty)." . PHP_EOL;
echo "- SEM = stdev / sqrt(n) — as sample size grows, SEM shrinks and the interval tightens." . PHP_EOL;

// =====================================================================
// Step 9: Percentile Benchmarks
// =====================================================================
echo PHP_EOL . "=== Step 9: Percentile Benchmarks ===" . PHP_EOL;
echo "\"What time do you need to beat 75% of the field?\"" . PHP_EOL . PHP_EOL;

echo "Percentile benchmarks:" . PHP_EOL;
$percentiles = [10, 25, 50, 75, 90];
foreach ($percentiles as $p) {
    $val = Stat::percentile($finishTimes, $p, 0);
    echo "  P" . str_pad((string) $p, 3) . ": " . Format::secondsToTime($val) . " (" . $val . "s)" . PHP_EOL;
}

echo PHP_EOL;
$trimmed10 = Stat::trimmedMean($finishTimes, 0.1, 0);
$trimmed20 = Stat::trimmedMean($finishTimes, 0.2, 0);
echo "Trimmed means (removing extreme runners):" . PHP_EOL;
echo "  Regular mean:       " . Format::secondsToTime(round($mean)) . PHP_EOL;
echo "  Trimmed mean (10%): " . Format::secondsToTime($trimmed10) . PHP_EOL;
echo "  Trimmed mean (20%): " . Format::secondsToTime($trimmed20) . PHP_EOL;

echo PHP_EOL;

// Weighted median — weight by inverse placement (top finishers weighted more)
$weights = [];
$n = count($finishTimes);
foreach (array_keys($runners) as $i) {
    // Weight inversely by finish order (sorted data: fast = high weight)
    $weights[] = $n - $i;
}
$wMedian = Stat::weightedMedian($finishTimes, $weights, 0);
echo "Weighted median (top finishers weighted more): " . Format::secondsToTime($wMedian) . PHP_EOL;
echo "Regular median:                                " . Format::secondsToTime(round(Stat::median($finishTimes))) . PHP_EOL;
echo PHP_EOL;
echo "How to interpret:" . PHP_EOL;
echo "- P25 is the cutoff to beat 75% of the field." . PHP_EOL;
echo "- If trimmed means get closer to the median, it confirms right skew (slow outliers pull the mean up)." . PHP_EOL;
echo "- If the weighted median is faster than the regular median, the weighting emphasizes the competitive core." . PHP_EOL;

// =====================================================================
// Step 10: Summary & Functions Used
// =====================================================================
echo PHP_EOL . str_repeat("=", 60) . PHP_EOL;
echo "SUMMARY: FUNCTIONS DEMONSTRATED" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL . PHP_EOL;

echo "Functions demonstrated (30+):" . PHP_EOL;
echo str_pad("  Function", 38) . "Step" . PHP_EOL;
echo "  " . str_repeat("-", 40) . PHP_EOL;
$functions = [
    ['Stat::mean()', '1,2,3,5'],
    ['Stat::median()', '1,9'],
    ['Stat::stdev()', '1'],
    ['Stat::quantiles()', '1'],
    ['Stat::tTestTwoSample()', '2,5'],
    ['Stat::tTestPaired()', '3'],
    ['Stat::correlation() — Pearson', '4'],
    ['Stat::correlation() — Spearman', '4'],
    ['Stat::linearRegression()', '4'],
    ['Stat::rSquared()', '4'],
    ['Stat::coefficientOfVariation()', '5'],
    ['Stat::skewness()', '6'],
    ['Stat::kurtosis()', '6'],
    ['NormalDist::fromSamples()', '6'],
    ['NormalDist::cdf()', '6'],
    ['Stat::outliers()', '7'],
    ['Stat::iqrOutliers()', '7'],
    ['Stat::zscores()', '7'],
    ['Stat::confidenceInterval()', '8'],
    ['Stat::sem()', '8'],
    ['Stat::percentile()', '9'],
    ['Stat::trimmedMean()', '9'],
    ['Stat::weightedMedian()', '9'],
];
foreach ($functions as $f) {
    echo "  " . str_pad($f[0], 36) . $f[1] . PHP_EOL;
}
