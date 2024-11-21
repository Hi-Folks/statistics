<?php

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Statistics;

it('can calculate statistics', function (): void {
    $s = Statistics::make(
        [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88, 76],
    );
    expect($s->count())->toEqual(12);
    expect($s->median())->toEqual(85.5);
    expect($s->firstQuartile())->toEqual(58.75);
    expect($s->thirdQuartile())->toEqual(92);
    expect($s->interquartileRange())->toEqual(33.25);

    expect($s->originalArray())->toHaveCount(12);

    $s = Statistics::make(
        [98, 90, 70, 18, 92, 92, 55, 83, 45, 95, 88],
    );
    expect($s->count())->toEqual(11);
    expect($s->median())->toEqual(88);
    expect($s->firstQuartile())->toEqual(55);
    expect($s->thirdQuartile())->toEqual(92);
    expect($s->interquartileRange())->toEqual(37);
    expect($s->originalArray())->toHaveCount(11);
});

it('can calculate statistics again', function (): void {
    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3, 5, 4, 7, 5, 2],
    );
    expect($s->count())->toEqual(6);
    expect($s->mean())->toEqual(13 / 3);
    expect($s->median())->toEqual(4.5);
    expect($s->mode())->toEqual(5);
    expect($s->min())->toEqual(2);
    expect($s->max())->toEqual(7);
    expect($s->range())->toEqual(5);
    expect($s->firstQuartile())->toEqual(2.75);
    expect($s->thirdQuartile())->toEqual(5.5);
});

it('can calculate statistics again and again', function (): void {
    // https://www.purplemath.com/modules/meanmode.htm
    $s = Statistics::make(
        [13, 18, 13, 14, 13, 16, 14, 21, 13],
    );
    expect($s->count())->toEqual(9);
    expect($s->mean())->toEqual(15);
    expect($s->median())->toEqual(14);
    expect($s->mode())->toEqual(13);
    expect($s->min())->toEqual(13);
    expect($s->max())->toEqual(21);
    expect($s->range())->toEqual(8);
    expect($s->firstQuartile())->toEqual(13);
    expect($s->thirdQuartile())->toEqual(17);

    $s = Statistics::make(
        [1, 2, 4, 7],
    );
    expect($s->count())->toEqual(4);
    expect($s->mean())->toEqual(3.5);
    expect($s->median())->toEqual(3);
    expect($s->mode())->toBeNull();
    expect($s->min())->toEqual(1);
    expect($s->max())->toEqual(7);
    expect($s->range())->toEqual(6);
});

it('can strip zeros', function (): void {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3, 5, 0, 0.1, 4, 7, 5, 2],
    )->stripZeroes();
    expect($s->count())->toEqual(7);
});
it('can calculate mean', function (): void {    // https://www.youtube.com/watch?v=6z7B7ADL6Lw&ab_channel=TheMathsProf
    $s = Statistics::make(
        [3, 5, 4, 7, 5, 2],
    );
    expect($s->count())->toEqual(6);
    expect($s->mean())->toEqual(13 / 3);
    $s = Statistics::make(
        [],
    );
    expect($s->count())->toEqual(0);
    expect(fn(): float|int|null => $s->mean())->toThrow(InvalidDataInputException::class);
});

it('can calculate mean again', function (): void { // https://docs.python.org/3/library/statistics.html#statistics.mean
    $s = Statistics::make(
        [1, 2, 3, 4, 4],
    );
    expect($s->mean())->toEqual(2.8);

    $s = Statistics::make(
        [-1.0, 2.5, 3.25, 5.75],
    );
    expect($s->mean())->toEqual(2.625);

    $s = Statistics::make(
        [0.5, 0.75, 0.625, 0.375],
    );
    expect($s->mean())->toEqual(0.5625);

    $s = Statistics::make(
        [3.5, 4.0, 5.25],
    );
    expect($s->mean())->toEqual(4.25);
});

it('can valuesToString', function (): void {
    $s = Statistics::make(
        [1, 2, 3, 4, 4],
    );
    expect($s->valuesToString(false))->toEqual('1,2,3,4,4');
    expect($s->valuesToString(3))->toEqual('1,2,3');
});

it('calculates Population standard deviation', function (): void {
    expect(
        Statistics::make(
            [1.5, 2.5, 2.5, 2.75, 3.25, 4.75],
        )->pstdev(),
    )->toEqual(0.986893273527251);
    expect(
        Statistics::make([1, 2, 4, 5, 8])->pstdev(4),
    )->toEqual(2.4495);
    expect(
        fn(): float => Statistics::make([])->pstdev(),
    )->toThrow(InvalidDataInputException::class);
    expect(
        Statistics::make([1])->pstdev(),
    )->toEqual(0);
    expect(
        Statistics::make([1, 2, 3, 3])->pstdev(7),
    )->toEqual(0.8291562);
});
it('calculates Sample standard deviation', function (): void {
    expect(
        Statistics::make([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])->stdev(),
    )->toEqual(1.0810874155219827);
    expect(
        Statistics::make([1, 2, 2, 4, 6])->stdev(),
    )->toEqual(2);
    expect(
        Statistics::make([1, 2, 4, 5, 8])->stdev(4),
    )->toEqual(2.7386);
    expect(
        fn(): float => Statistics::make([])->stdev(),
    )->toThrow(InvalidDataInputException::class);
    expect(
        fn(): float => Statistics::make([1])->stdev(),
    )->toThrow(InvalidDataInputException::class);
});

it('calculates variance', function (): void {
    expect(
        Statistics::make([2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5])->variance(),
    )->toEqual(1.3720238095238095);
});

it('calculates pvariance', function (): void {
    expect(
        Statistics::make([0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25])->pvariance(),
    )->toEqual(1.25);
    expect(
        Statistics::make([1, 2, 3, 3])->pvariance(),
    )->toEqual(0.6875);
});

it('calculates geometric mean', function (): void {
    expect(
        Statistics::make([54, 24, 36])->geometricMean(2),
    )->toEqual(36);
    expect(
        Statistics::make([4, 8, 3, 9, 17])->geometricMean(2),
    )->toEqual(6.81);
    expect(
        fn(): float => Statistics::make([])->geometricMean(),
    )->toThrow(InvalidDataInputException::class);
});

it('calculates harmonic mean', function (): void {
    expect(
        Statistics::make([40, 60])->harmonicMean(1),
    )->toEqual(48.0);
    expect(
        fn(): float => Statistics::make([])->harmonicMean(),
    )->toThrow(InvalidDataInputException::class);
});

it('can distinct numeric array', function (): void {
    expect(Statistics::make([1, 2, 3])->numericalArray())->toEqual([1, 2, 3]);
    expect(Statistics::make([1, '2', 3])->numericalArray())->toEqual([1, '2', 3]);
    expect(Statistics::make([])->numericalArray())->toEqual([]);
    expect(fn(): array => Statistics::make([1, 'some string', 3])->numericalArray())->toThrow(InvalidDataInputException::class);
});
