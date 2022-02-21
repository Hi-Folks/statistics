<?php

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Stat;

it('can calculate mean (static)', function () {
    expect(
        Stat::mean([1, 2, 3, 4, 4])
    )->toEqual(2.8);
    expect(
        Stat::mean([-1.0, 2.5, 3.25, 5.75])
    )->toEqual(2.625);
    expect(
        fn () => Stat::mean([])
    )->toThrow(InvalidDataInputException::class);
});

it('can calculate median (static)', function () {
    expect(
        Stat::median([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::median([1, 3, 5, 7])
    )->toEqual(4);
    expect(
        fn () => Stat::median([])
    )->toThrow(InvalidDataInputException::class);
});
it('can calculate median low (static)', function () {
    expect(
        Stat::medianLow([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::medianLow([1, 3, 5, 7])
    )->toEqual(3);
    expect(
        fn () => Stat::medianLow([])
    )->toThrow(InvalidDataInputException::class);
});
it('can calculate median high (static)', function () {
    expect(
        Stat::medianHigh([1, 3, 5])
    )->toEqual(3);
    expect(
        Stat::medianHigh([1, 3, 5, 7])
    )->toEqual(5);
    expect(
        fn () => Stat::medianHigh([])
    )->toThrow(InvalidDataInputException::class);
});

it('calculates mode (static)', function () {
    expect(
        Stat::mode([1, 1, 2, 3, 3, 3, 3, 4])
    )->toEqual(3);
    expect(
        fn () => Stat::mode([])
    )->toThrow(InvalidDataInputException::class);
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
        fn () => Stat::multimode([])
    )->toThrow(InvalidDataInputException::class);
});
it('calculates Population standard deviation (static)', function () {
    expect(
        Stat::pstdev([1.5, 2.5, 2.5, 2.75, 3.25, 4.75])
    )->toEqual(0.986893273527251);
    expect(
        Stat::pstdev([1, 2, 4, 5, 8], 4)
    )->toEqual(2.4495);
    expect(
        fn () => Stat::pstdev([])
    )->toThrow(InvalidDataInputException::class);
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
        fn () => Stat::stdev([])
    )->toThrow(InvalidDataInputException::class);
    expect(
        fn () => Stat::stdev([1])
    )->toThrow(InvalidDataInputException::class);
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
        fn () => Stat::geometricMean([])
    )->toThrow(InvalidDataInputException::class);
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
        fn () => Stat::harmonicMean([])
    )->toThrow(InvalidDataInputException::class);
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
        fn () => Stat::quantiles([1])
    )->toThrow(InvalidDataInputException::class);
    expect(
        fn () => Stat::quantiles([1,2,3], 0)
    )->toThrow(InvalidDataInputException::class);
});


it('calculates first quartiles (static)', function () {
    $q = Stat::firstQuartile([98, 90, 70,18,92,92,55,83,45,95,88,76]);
    expect($q)->toEqual(58.75);
    expect(
        fn () => Stat::firstQuartile([])
    )->toThrow(InvalidDataInputException::class);
});

it('calculates covariance (static)', function () {
    $covariance = Stat::covariance(
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 1, 2, 3, 1, 2, 3]
    );
    expect($covariance)->toEqual(0.75);

    $covariance = Stat::covariance(
        [9, 8, 7, 6, 5, 4, 3, 2, 1],
        [1, 2, 3, 4, 5, 6, 7, 8, 9]
    );
    expect($covariance)->toEqual(-7.5);

    $covariance = Stat::covariance(
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [9, 8, 7, 6, 5, 4, 3, 2, 1]
    );
    expect($covariance)->toEqual(-7.5);
});

it('calculates covariance, wrong usage (static)', function () {
    expect(
        fn () => Stat::covariance(
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
            [1, 2, 3, 4, 5, 6, 7, 8]
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::covariance(
            [],
            []
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::covariance(
            [3],
            [3]
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::covariance(
            ['a', 1],
            ['b', 2]
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::covariance(
            [3, 1],
            ['b', 2]
        )
    )->toThrow(InvalidDataInputException::class);

});

it('calculates correlation (static)', function () {
    $correlation = Stat::correlation(
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 4, 5, 6, 7, 8, 9]
    );
    expect($correlation)->toBeFloat();
    expect($correlation)->toEqual(1);

    $correlation = Stat::correlation(
        [1, 2, 3, 4, 5, 6, 7, 8, 9],
        [9, 8, 7, 6, 5, 4, 3, 2, 1]
    );
    expect($correlation)->toBeFloat();
    expect($correlation)->toEqual(-1);

    $correlation = Stat::correlation(
        [3, 6, 9],
        [70, 75, 80]
    );
    expect($correlation)->toBeFloat();
    expect($correlation)->toEqual(1);

    $correlation = Stat::correlation(
        [20, 23, 8, 29, 14, 11, 11, 20, 17, 17],
        [30, 35, 21, 33, 33, 26, 22, 31, 33, 36]
    );
    expect($correlation)->toBeFloat();
    expect($correlation)->toEqual(0.71);
});

it('calculates correlation, wrong usage (static)', function () {

    expect(
        fn () => Stat::correlation(
            [9, 8, 7, 6, 5, 4, 3, 2, 1],
            [1, 2, 3, 4, 5, 6, 7, 8]
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::correlation(
            [],
            []
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::correlation(
            [3],
            [3]
        )
    )->toThrow(InvalidDataInputException::class);

    expect(
        fn () => Stat::correlation(
            [3, 1, 2],
            [2, 2, 2]
        )
    )->toThrow(InvalidDataInputException::class);

});
