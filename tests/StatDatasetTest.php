<?php

use HiFolks\Statistics\Stat;

it('Calculating Mean no dataset', function () {
    expect(Stat::mean([1, 2, 3, 4, 4]))->toEqual(2.8);
    expect(Stat::mean([-1.0, 2.5, 3.25, 5.75]))->toEqual(2.625);
});

it('Calculating Mean no dataset one expect', function () {
    expect(Stat::mean([1, 2, 3, 4, 4]))
        ->toEqual(2.8)
        ->and(Stat::mean([-1.0, 2.5, 3.25, 5.75]))
        ->toEqual(2.625);
});

it('Calculating Mean', function (array $input, float $result) {
    expect(Stat::mean($input))->toEqual($result);
})->with([
    [ [1, 2, 3, 4, 4], 2.8],
    [ [-1.0, 2.5, 3.25, 5.75], 2.625],
]);
it('Calculating Operations dynamically', function (string $methodName, array $input, float $result) {
    expect(call_user_func([Stat::class, $methodName], $input))->toEqual($result);
})->with([
    [ "mean", [1, 2, 3, 4, 4], 2.8],
    [ "mean", [-1.0, 2.5, 3.25, 5.75], 2.625],
    [ "median", [1, 3, 5], 3],
    [ "median", [1, 3, 5, 7], 4],
    [ "medianLow", [1, 3, 5], 3],
    [ "medianLow", [1, 3, 5, 7], 3],



]);
