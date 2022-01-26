<?php

use HiFolks\Statistics\Freq;

it('can calculate freq table (static)', function () {
    expect(
        Freq::frequencies([1, 2, 3, 4, 4])
    )->toMatchArray([4 => 2,3 => 1, 1 => 1, 2 => 1]);
    expect(
        Freq::frequencies([])
    )->toMatchArray([]);
    $result = Freq::frequencies(["red", "blue", "blue", "red", "green", "red", "red"]);
    expect(
        $result
    )->toMatchArray(["red" => 4,"blue" => 2, "green" => 1]);
    expect(
        $result
    )->toHaveCount(3);

    expect(
        $result["red"]
    )->toEqual(4);
    expect(
        $result["blue"]
    )->toEqual(2);
    expect(
        $result["green"]
    )->toEqual(1);

    $result = Freq::frequencies([2.1, 2.7, 1.4, 2.45], true);
    expect(
        $result
    )->toMatchArray([2 => 3,1 => 1]);
    expect(
        $result
    )->toHaveCount(2);
});

it('can calculate relativefreq table (static)', function () {
    expect(
        Freq::relativeFrequencies([1, 2, 3, 4, 4])
    )->toMatchArray([4 => 40,3 => 20, 1 => 20, 2 => 20]);
    expect(
        Freq::relativeFrequencies([])
    )->toMatchArray([]);
    $result = Freq::relativeFrequencies(["red", "blue", "blue", "red", "green", "red", "red"], 2);
    expect(
        $result
    )->toMatchArray(["red" => 57.14,"blue" => 28.57, "green" => 14.29]);
    expect(
        $result
    )->toHaveCount(3);

    expect(
        $result["red"]
    )->toEqual(57.14);
    expect(
        $result["blue"]
    )->toEqual(28.57);
    expect(
        $result["green"]
    )->toEqual(14.29);

    $result = Freq::relativeFrequencies([2.1, 2.7, 1.4, 2.45], true);
    expect(
        $result
    )->toMatchArray([2 => 75,1 => 25]);
    expect(
        $result
    )->toHaveCount(2);
});
