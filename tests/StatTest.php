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
