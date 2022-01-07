<?php

use HiFolks\Statistics\StatisticsClass;

it('can calculate frequencies', function () {
    $s = StatisticsClass::make(
        [98, 90, 70,18,92,92,55,83,45,95,88,76]
    );
    expect($s->getCount())->toEqual(12);
    expect($s->getMedian())->toEqual(85.5);
    expect($s->getLowerPercentile())->toEqual(62.5);
    expect($s->getHigherPercentile())->toEqual(92);
    expect($s->getInterQuartileRange())->toEqual(29.5);
});

it('can calculate frequencies again', function () {
    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = StatisticsClass::make(
        [3,5,4,7,5,2]
    );
    expect($s->getCount())->toEqual(6);
    expect($s->getMean())->toEqual(13 / 3);
    expect($s->getMedian())->toEqual(4.5);
    expect($s->getMode())->toEqual(5);
    expect($s->getMin())->toEqual(2);
    expect($s->getMax())->toEqual(7);
    expect($s->getRange())->toEqual(5);
});

it('can strip zeros', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = StatisticsClass::make(
        [3,5,0,0.1,4,7,5,2]
    )->stripZeroes();
    expect($s->getCount())->toEqual(7);
});
it('can calculate mean', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = StatisticsClass::make(
        [3,5,4,7,5,2]
    );
    expect($s->getCount())->toEqual(6);
    expect($s->getMean())->toEqual(13 / 3);
    $s = StatisticsClass::make(
        []
    );
    expect($s->getCount())->toEqual(0);
    expect($s->getMean())->toBeNull();
});