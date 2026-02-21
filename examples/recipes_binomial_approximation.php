<?php

require __DIR__ . '/../vendor/autoload.php';

use HiFolks\Statistics\NormalDist;

/**
 * Recipe: Approximating Binomial Distributions
 *
 * Adapted from the Python statistics module "Examples and Recipes":
 * https://docs.python.org/3/library/statistics.html#examples-and-recipes
 *
 * NormalDist can be used to approximate binomial distributions
 * when the sample size is large (via the Central Limit Theorem).
 *
 * Scenario: a]conference has 750 attendees. 65% prefer Python
 * and 35% prefer Ruby. The "Python" room holds 500 people.
 * What is the probability that the room will stay within capacity?
 */
echo "=== Approximating Binomial Distributions ===" . PHP_EOL . PHP_EOL;

$n = 750;            // Sample size (attendees)
$p = 0.65;           // Probability of preferring Python
$q = 1.0 - $p;       // Probability of preferring Ruby
$k = 500;            // Room capacity

// For a binomial distribution B(n, p):
//   mean  = n * p
//   sigma = sqrt(n * p * q)
$mu = $n * $p;
$sigma = sqrt($n * $p * $q);

echo "Binomial parameters:" . PHP_EOL;
echo "  n = " . $n . " (attendees)" . PHP_EOL;
echo "  p = " . $p . " (Python preference)" . PHP_EOL;
echo "  Expected Python fans: " . $mu . PHP_EOL;
echo "  Standard deviation: " . round($sigma, 2) . PHP_EOL;
echo PHP_EOL;

// Normal approximation with continuity correction
$normal = new NormalDist($mu, $sigma);
$probNormal = $normal->cdf($k + 0.5);
echo "Normal approximation: P(X <= " . $k . ") = "
    . round($probNormal, 4) . PHP_EOL;

// Exact binomial calculation using log-space arithmetic.
// P(X <= k) = sum from r=0 to k of C(n,r) * p^r * q^(n-r)
// We use Stirling's log-gamma via log() of factorials to avoid overflow.

// Build log-factorial lookup table
$logFact = [0.0]; // log(0!) = 0
for ($i = 1; $i <= $n; $i++) {
    $logFact[$i] = $logFact[$i - 1] + log($i);
}

$logTerms = [];
for ($r = 0; $r <= $k; $r++) {
    // log(C(n,r)) = log(n!) - log(r!) - log((n-r)!)
    $logBinom = $logFact[$n] - $logFact[$r] - $logFact[$n - $r];
    $logTerms[] = $logBinom + $r * log($p) + ($n - $r) * log($q);
}
// Log-sum-exp for numerical stability
$maxLog = max($logTerms);
$sum = 0.0;
foreach ($logTerms as $logTerm) {
    $sum += exp($logTerm - $maxLog);
}
$probExact = exp($maxLog + log($sum));

echo "Exact binomial:        P(X <= " . $k . ") = "
    . round($probExact, 4) . PHP_EOL;

// Monte Carlo simulation approximation
$seed = 8675309;
mt_srand($seed);
$trials = 10_000;
$successes = 0;
for ($i = 0; $i < $trials; $i++) {
    $count = 0;
    for ($j = 0; $j < $n; $j++) {
        if (mt_rand() / mt_getrandmax() < $p) {
            $count++;
        }
    }
    if ($count <= $k) {
        $successes++;
    }
}
$probSimulation = $successes / $trials;
echo "Simulation (" . $trials . " trials): P(X <= " . $k . ") = "
    . round($probSimulation, 4) . PHP_EOL;

echo PHP_EOL . "All three methods should give approximately the same result (~0.84)."
    . PHP_EOL;

// --- Additional: What capacity is needed for 99% confidence? ---
echo PHP_EOL . "--- Capacity Planning ---" . PHP_EOL;
$needed = $normal->invCdfRounded(0.99, 0);
echo "For 99% confidence, room capacity should be: "
    . $needed . " seats" . PHP_EOL;
