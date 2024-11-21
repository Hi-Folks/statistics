<?php

require __DIR__ . '/../vendor/autoload.php';

use HiFolks\Statistics\Freq;
use HiFolks\Statistics\Stat;

$freq = Freq::frequencies(
    ['red', 'blue', 'blue', 'red', 'green', 'red', 'red'],
);
var_dump($freq);
$mode = Stat::mode(
    ['red', 'blue', 'blue', 'red', 'green', 'red', 'red'],
);

var_dump($mode);
