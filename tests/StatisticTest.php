<?php

use HiFolks\Statistics\Statistics;

it('can calculate statistics', function () {
    $s = Statistics::make(
        [98, 90, 70,18,92,92,55,83,45,95,88,76]
    );
    expect($s->count())->toEqual(12);
    expect($s->median())->toEqual(85.5);
    expect($s->lowerPercentile())->toEqual(62.5);
    expect($s->higherPercentile())->toEqual(92);
    expect($s->getInterQuartileRange())->toEqual(29.5);
});

it('can calculate statistics again', function () {
    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3,5,4,7,5,2]
    );
    expect($s->count())->toEqual(6);
    expect($s->mean())->toEqual(13 / 3);
    expect($s->median())->toEqual(4.5);
    expect($s->mode())->toEqual(5);
    expect($s->getMin())->toEqual(2);
    expect($s->getMax())->toEqual(7);
    expect($s->getRange())->toEqual(5);
});

it('can calculate statistics again and again', function () {
    // https://www.purplemath.com/modules/meanmode.htm
    $s = Statistics::make(
        [13, 18, 13, 14, 13, 16, 14, 21, 13]
    );
    expect($s->count())->toEqual(9);
    expect($s->mean())->toEqual(15);
    expect($s->median())->toEqual(14);
    expect($s->mode())->toEqual(13);
    expect($s->getMin())->toEqual(13);
    expect($s->getMax())->toEqual(21);
    expect($s->getRange())->toEqual(8);

    $s = Statistics::make(
        [1, 2, 4, 7]
    );
    expect($s->count())->toEqual(4);
    expect($s->mean())->toEqual(3.5);
    expect($s->median())->toEqual(3);
    expect($s->mode())->toBeNull();
    expect($s->getMin())->toEqual(1);
    expect($s->getMax())->toEqual(7);
    expect($s->getRange())->toEqual(6);
});



it('can strip zeros', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3,5,0,0.1,4,7,5,2]
    )->stripZeroes();
    expect($s->count())->toEqual(7);
});
it('can calculate mean', function () {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3,5,4,7,5,2]
    );
    expect($s->count())->toEqual(6);
    expect($s->mean())->toEqual(13 / 3);
    $s = Statistics::make(
        []
    );
    expect($s->count())->toEqual(0);
    expect($s->mean())->toBeNull();
});

it('can calculate mean again', function () { // https://docs.python.org/3/library/statistics.html#statistics.mean
    $s = Statistics::make(
        [1, 2, 3, 4, 4]
    );
    expect($s->mean())->toEqual(2.8);

    $s = Statistics::make(
        [-1.0, 2.5, 3.25, 5.75]
    );
    expect($s->mean())->toEqual(2.625);

    $s = Statistics::make(
        [0.5, 0.75, 0.625, 0.375]
    );
    expect($s->mean())->toEqual(0.5625);

    $s = Statistics::make(
        [3.5, 4.0, 5.25]
    );
    expect($s->mean())->toEqual(4.25);
});
