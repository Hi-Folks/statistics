<?php

require __DIR__.'/../vendor/autoload.php';

use HiFolks\Statistics\Freq;
use HiFolks\Statistics\Statistics;

$fruits = ['🍈', '🍈', '🍈', '🍉', '🍉', '🍉', '🍉', '🍉', '🍌'];
$freqTable = Freq::frequencies($fruits);
print_r($freqTable);
/*
Array
(
    [🍈] => 3
    [🍉] => 5
    [🍌] => 1
)
 */

$freqTable = Freq::relativeFrequencies($fruits, 2);
print_r($freqTable);
/*
Array
(
    [🍈] => 33.33
    [🍉] => 55.56
    [🍌] => 11.11
)
 */

$s = Statistics::make(
    [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76]
);
$a = $s->frequencies();
print_r($a);
/*
Array
(
    [18] => 1
    [45] => 1
    [55] => 1
    [70] => 1
    [76] => 1
    [83] => 1
    [88] => 1
    [90] => 1
    [92] => 2
    [95] => 1
    [98] => 1
)
 */

$a = $s->relativeFrequencies();
print_r($a);
/*
Array
(
    [18] => 8.3333333333333
    [45] => 8.3333333333333
    [55] => 8.3333333333333
    [70] => 8.3333333333333
    [76] => 8.3333333333333
    [83] => 8.3333333333333
    [88] => 8.3333333333333
    [90] => 8.3333333333333
    [92] => 16.666666666667
    [95] => 8.3333333333333
    [98] => 8.3333333333333
)
 */
