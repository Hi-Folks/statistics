<?php

require(__DIR__ . "/../vendor/autoload.php");

use HiFolks\Statistics\Stat;
use HiFolks\Statistics\Freq;

$freq = Freq::frequencies(
    ["red", "blue", "blue", "red", "green", "red", "red"]
);
var_dump($freq);
$mode = Stat::mode(
    ["red", "blue", "blue", "red", "green", "red", "red"]
);

var_dump($mode);
