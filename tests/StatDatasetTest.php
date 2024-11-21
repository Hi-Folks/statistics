<?php

use HiFolks\Statistics\Stat;

describe('Calculating Stat operation', function () {
    it('Mean', function () {
        expect(Stat::mean([1, 2, 3, 4, 4]))->toEqual(2.8);
        expect(Stat::mean([-1.0, 2.5, 3.25, 5.75]))->toEqual(2.625);
    });

    it('Mean chain expect', function () {
        expect(Stat::mean([1, 2, 3, 4, 4]))
            ->toEqual(2.8)
            ->and(Stat::mean([-1.0, 2.5, 3.25, 5.75]))
            ->toEqual(2.625);
    });

    it('Mean dataset', function (array $input, float $result) {
        expect(Stat::mean($input))->toEqual($result);
    })->with([
        [[1, 2, 3, 4, 4], 2.8],
        [[-1.0, 2.5, 3.25, 5.75], 2.625],
    ]);

    it('Dynamic operation', function (string $methodName, array $input, float $result) {
        expect(call_user_func([Stat::class, $methodName], $input))->toEqual($result);
    })->with([
        ["mean", [1, 2, 3, 4, 4], 2.8],
        ["mean", [-1.0, 2.5, 3.25, 5.75], 2.625],
        ["median", [1, 3, 5], 3],
        ["median", [1, 3, 5, 7], 4],
        ["medianLow", [1, 3, 5], 3],
        ["medianLow", [1, 3, 5, 7], 3],
    ]);

    it('Dynamic operation with external dataset', function (string $methodName, array $input, float $result) {
        expect(
            call_user_func("HiFolks\Statistics\Stat::" . $methodName, $input),
        )->toEqual($result);

        expect(
            call_user_func([Stat::class, $methodName], $input),
        )->toEqual($result);
    })->with('input1');
});
