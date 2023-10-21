<?php

require __DIR__ . '/../vendor/autoload.php';

$data = [55, 70, 57, 73, 55, 59, 64, 72,
    60, 48, 58, 54, 69, 51, 63, 78,
    75, 64, 65, 57, 71, 78, 76, 62,
    49, 66, 62, 76, 61, 63, 63, 76,
    52, 76, 71, 61, 53, 56, 67, 71, ];
$result = \HiFolks\Statistics\Freq::frequencyTable($data, 7);
echo min($data) . PHP_EOL;
echo max($data) . PHP_EOL;
print_r($result);

$data = [1, 1, 1, 4, 4, 5, 5, 5, 6, 7, 8, 8, 8, 9, 9, 9, 9, 9, 9, 10, 10, 11, 12, 12,
    13, 14, 14, 15, 15, 16, 16, 16, 16, 17, 17, 17, 18, 18, ];
$result = \HiFolks\Statistics\Freq::frequencyTableBySize($data, 4);
print_r($result);
$result = \HiFolks\Statistics\Freq::frequencyTable($data, 5);
echo count($data) . PHP_EOL;
echo min($data) . PHP_EOL;
echo max($data) . PHP_EOL;
print_r($result);
