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
