<?php

use HiFolks\Statistics\Statistics;

it('can calculate frequencies', function () {
    $s = Statistics::make(
        [98, 90, 70,18,92,92,55,83,45,95,88,76]
    );
    $a = $s->frequencies();
    expect($a[92])->toEqual(2);
    expect($a)->toHaveCount(11);
});

it('can calculate relative frequencies', function () {
    $s = Statistics::make(
        [3,4,3,1]
    );
    $a = $s->relativeFrequencies();
    expect($a[3])->toEqual(50);
    expect($a)->toHaveCount(3);
    expect($s->originalArray())->toHaveCount(4);
});

it('can calculate cumulative frequencies', function () {
    $s = Statistics::make(
        [3,4,3,1]
    );
    $a = $s->getCumulativeFrequences();

    expect($a[3])->toEqual(3);
    expect($a)->toHaveCount(3);
    expect($s->originalArray())->toHaveCount(4);
});

it('can calculate cumulative relative frequencies', function () {
    $s = Statistics::make(
        [3,4,3,1]
    );
    $a = $s->cumulativeRelativeFrequencies();

    expect($a[3])->toEqual(75);
    expect($a)->toHaveCount(3);
    expect($s->originalArray())->toHaveCount(4);
});

it('can calculate lowerPercentile', function () {
    $s = Statistics::make(
        [3,4,3,1]
    );
    $a = $s->lowerPercentile();
    expect($a)->toEqual(2);

    $s = Statistics::make(
        [3,4,3]
    );
    $a = $s->lowerPercentile();
    expect($a)->toEqual(3);

    $s = Statistics::make(
        []
    );
    $a = $s->lowerPercentile();
    expect($a)->toBeNull();
});
it('can calculate higherPercentile', function () {
    $s = Statistics::make(
        [3,4,3,1]
    );
    $a = $s->higherPercentile();
    expect($a)->toEqual(3.5);

    $s = Statistics::make(
        [3,4,3]
    );
    $a = $s->higherPercentile();
    expect($a)->toEqual(4);

    $s = Statistics::make(
        []
    );
    $a = $s->higherPercentile();

    expect($a)->toBeNull();
});
