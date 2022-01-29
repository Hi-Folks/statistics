<?php
require(__DIR__ . "/../vendor/autoload.php");


use HiFolks\Statistics\Stat;
$mean = Stat::mean([1, 2, 3, 4, 4]);
// 2.8
$mean = Stat::mean([-1.0, 2.5, 3.25, 5.75]);
// 2.625
$mean = Stat::geometricMean([54, 24, 36], 1);
// 36.0
$mean = Stat::harmonicMean([40, 60], null, 1);
// 48.0
$mean = Stat::harmonicMean([40, 60], [5, 30], 1);
// 56.0
$median = Stat::median([1, 3, 5, 7]);
// 4
$median = Stat::medianLow([1, 3, 5, 7]);
// 3
$median = Stat::medianHigh([1, 3, 5, 7]);
// 5
$stdev = Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75], 4);
// 0.9869
$stdev = Stat::stdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75], 4);
// 1.0811
$variance = Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25]);
// 1.25
$variance = Stat::variance([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5]);
// 1.3720238095238095
