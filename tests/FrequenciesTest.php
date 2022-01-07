<?php

use HiFolks\Statistics\Statistics;

it('can calculate frequencies', function () {
    $s = Statistics::make(
        [98, 90, 70,18,92,92,55,83,45,95,88,76]
    );
    $a = $s->getFrequencies();
    expect($a[92])->toEqual(2);
    expect($a)->toHaveCount(11);
});
