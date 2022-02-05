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

it('can calculate grouped frequency table (static)', function () {
    $data = [1,1,1,4,4,5,5,5,6,7,8,8,8,9,9,9,9,9,9,10,10,11,12,12,
        13,14,14,15,15,16,16,16,16,17,17,17,18,18, ];
    $table = Freq::frequencyTable($data, 7);
    expect(
        $table
    )->toHaveCount(6);
    expect(
        $table
    )->toMatchArray([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTable($data, 6);
    expect(
        $table
    )->toHaveCount(6);
    expect(
        $table
    )->toMatchArray([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTable($data, 8);
    expect(
        $table
    )->toHaveCount(6);
    expect(
        $table
    )->toMatchArray([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTable($data, 3);
    expect(
        $table
    )->toHaveCount(3);
    expect(
        $table
    )->toMatchArray([1 => 9, 7 => 15, 13 => 14]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTable($data);
    expect(
        $table
    )->toHaveCount(18);
    expect(
        $table
    )->toMatchArray([1 => 3, 2 => 0, 3 => 0, 4 => 2,5 => 3, 6 => 1, 7 => 1, 8 => 3, 9 => 6,
        10 => 2, 11 => 1, 12 => 2, 13 => 1, 14 => 2, 15 => 2, 16 => 4, 17 => 3, 18 => 2, ]);
});

it('can calculate grouped frequency table by size (static)', function () {
    $data = [1,1,1,4,4,5,5,5,6,7,8,8,8,9,9,9,9,9,9,10,10,11,12,12,
        13,14,14,15,15,16,16,16,16,17,17,17,18,18, ];
    $table = Freq::frequencyTableBySize($data, 4);
    expect(
        $table
    )->toHaveCount(5);
    expect(
        $table
    )->toMatchArray([1 => 5, 5 => 8, 9 => 11, 13 => 9, 17 => 5]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTableBySize($data, 5);
    expect(
        $table
    )->toHaveCount(4);
    expect(
        $table
    )->toMatchArray([1 => 8, 6 => 13, 11 => 8, 16 => 9]);
    expect(
        array_sum($table)
    )->toEqual(count($data));

    $table = Freq::frequencyTableBySize($data, 8);
    expect(
        $table
    )->toHaveCount(3);
    expect(
        $table
    )->toMatchArray([1 => 13, 9 => 20, 17 => 5]);
    expect(
        array_sum($table)
    )->toEqual(count($data));
});
