<?php

use HiFolks\Statistics\Statistics;

it('can calculate statistics', function () {
    $s = Statistics::make(
        [98, 90, 70,18,92,92,55,83,45,95,88,76]
    );
    expect($s->getCount())->toEqual(12);
    expect($s->getMedian())->toEqual(85.5);
    expect($s->getLowerPercentile())->toEqual(62.5);
    expect($s->getHigherPercentile())->toEqual(92);
    expect($s->getInterQuartileRange())->toEqual(29.5);
});

it('can calculate statistics again', function () {
    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
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

it('can calculate statistics again and again', function () {
    // https://www.purplemath.com/modules/meanmode.htm
    $s = Statistics::make(
        [13, 18, 13, 14, 13, 16, 14, 21, 13]
    );
    expect($s->getCount())->toEqual(9);
    expect($s->getMean())->toEqual(15);
    expect($s->getMedian())->toEqual(14);
    expect($s->getMode())->toEqual(13);
    expect($s->getMin())->toEqual(13);
    expect($s->getMax())->toEqual(21);
    expect($s->getRange())->toEqual(8);

    $s = Statistics::make(
        [1, 2, 4, 7]
    );
    expect($s->getCount())->toEqual(4);
    expect($s->getMean())->toEqual(3.5);
    expect($s->getMedian())->toEqual(3);
    expect($s->getMode())->toBeNull();
    expect($s->getMin())->toEqual(1);
    expect($s->getMax())->toEqual(7);
    expect($s->getRange())->toEqual(6);
});



it('can strip zeros', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3,5,0,0.1,4,7,5,2]
    )->stripZeroes();
    expect($s->getCount())->toEqual(7);
});
it('can calculate mean', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3,5,4,7,5,2]
    );
    expect($s->getCount())->toEqual(6);
    expect($s->getMean())->toEqual(13 / 3);
    $s = Statistics::make(
        []
    );
    expect($s->getCount())->toEqual(0);
    expect($s->getMean())->toBeNull();
});
