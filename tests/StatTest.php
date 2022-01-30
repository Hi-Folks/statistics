<?php

use HiFolks\Statistics\Stat;

it('can calculate mean (static)', function () {
    expect(
        Stat::mean([1, 2, 3, 4, 4])
    )->toEqual(2.8);
    expect(
        Stat::mean([-1.0, 2.5, 3.25, 5.75])
    )->toEqual(2.625);
    expect(
        Stat::mean([])
    )->toBeNull();
});

it('can calculate median (static)', function () {
    expect(
        Stat::median([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::median([1, 3, 5, 7])
    )->toEqual(4);
    expect(
        Stat::median([])
    )->toBeNull();
});
it('can calculate median low (static)', function () {
    expect(
        Stat::medianLow([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::medianLow([1, 3, 5, 7])
    )->toEqual(3);
    expect(
        Stat::medianLow([])
    )->toBeNull();
});
it('can calculate median high (static)', function () {
    expect(
        Stat::medianHigh([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::medianHigh([1, 3, 5, 7])
    )->toEqual(5);
    expect(
        Stat::medianHigh([])
    )->toBeNull();
});

it('calculates mode (static)', function () {
    expect(
        Stat::mode([1, 1, 2, 3, 3, 3, 3, 4])
    )->toEqual(3);
    expect(
        Stat::mode([])
    )->toBeNull();
    expect(
        Stat::mode([1,2,3])
    )->toBeNull();
    expect(
        Stat::mode(["red", "blue", "blue", "red", "green", "red", "red"])
    )->toEqual("red");
});

it('calculates multimode (static)', function () {
    expect(
        Stat::multimode([1, 1, 2, 3, 3, 3, 3, 4])
    )->toMatchArray([3]);
    expect(
        Stat::multimode([1, 1, 2, 3, 3, 3, 3, 1, 1, 4])
    )->toMatchArray([1, 3]);
    $result = Stat::multimode(str_split('aabbbbccddddeeffffgg'));
    expect(
        $result
    )->toMatchArray(['b', 'd', 'f']);
    expect(
        $result
    )->toHaveCount(3);

    expect(
        $result[0]
    )->toEqual('b');
    expect(
        $result[1]
    )->toEqual('d');
    expect(
        $result[2]
    )->toEqual('f');

    expect(
        Stat::multimode([])
    )->toMatchArray([]);
});
it('calculates Population standard deviation (static)', function () {
    expect(
        Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])
    )->toEqual(0.986893273527251);
    expect(
        Stat::pstdev([1, 2, 4, 5, 8], 4)
    )->toEqual(2.4495);
    expect(
        Stat::pstdev([])
    )->toBeNull();
    expect(
        Stat::pstdev([1])
    )->toEqual(0);
    expect(
        Stat::pstdev([1, 2, 3, 3], 7)
    )->toEqual(0.8291562);
});
it('calculates Sample standard deviation (static)', function () {
    expect(
        Stat::stdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])
    )->toEqual(1.0810874155219827);
    expect(
        Stat::stdev([1, 2, 2, 4, 6])
    )->toEqual(2);
    expect(
        Stat::stdev([1, 2, 4, 5, 8], 4)
    )->toEqual(2.7386);
    expect(
        Stat::stdev([])
    )->toBeNull();
    expect(
        Stat::stdev([1])
    )->toBeNull();
});

it('calculates variance (static)', function () {
    expect(
        Stat::variance([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5])
    )->toEqual(1.3720238095238095);
});

it('calculates pvariance (static)', function () {
    expect(
        Stat::pvariance([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25])
    )->toEqual(1.25);
    expect(
        Stat::pvariance([1, 2, 3, 3])
    )->toEqual(0.6875);
});

it('calculates geometric mean (static)', function () {
    expect(
        Stat::geometricMean([54, 24, 36])
    )->toEqual(36);
    expect(
        Stat::geometricMean([])
    )->toBeNull();
});
it('calculates harmonic mean (static)', function () {
    expect(
        Stat::harmonicMean([40, 60])
    )->toEqual(48);
    expect(
        Stat::harmonicMean([10,100,0,1])
    )->toEqual(0);
    expect(
        Stat::harmonicMean([40, 60], [5, 30])
    )->toEqual(56);
    expect(
        Stat::harmonicMean([60, 40], [7, 3], 1)
    )->toEqual(52.2);
    expect(
        Stat::harmonicMean([])
    )->toBeNull();
});

it('calculates quantiles (static)', function () {
    $q = Stat::quantiles([98, 90, 70,18,92,92,55,83,45,95,88,76]);
    expect($q[0])->toEqual(58.75);
    expect($q[1])->toEqual(85.5);
    expect($q[2])->toEqual(92);
    $q = Stat::quantiles([98, 90, 70,18,92,92,55,83,45,95,88]);
    expect($q[0])->toEqual(55);
    expect($q[1])->toEqual(88);
    expect($q[2])->toEqual(92);
    $q = Stat::quantiles([1,2]);
    expect($q[0])->toEqual(0.75);
    expect($q[1])->toEqual(1.5);
    expect($q[2])->toEqual(2.25);
    $q = Stat::quantiles([1,2,4]);
    expect($q[0])->toEqual(1);
    expect($q[1])->toEqual(2);
    expect($q[2])->toEqual(4);
    expect(
        Stat::quantiles([1])
    )->toBeNull();
});
