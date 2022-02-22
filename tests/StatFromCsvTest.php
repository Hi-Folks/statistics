<?php


use HiFolks\Statistics\Stat;

it('parse CSV', function () {
    $row = 0;

    if (($handle = fopen(getcwd()."/tests/data/income.data.csv", "r")) !== false) {
        $x = [];
        $y = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $num = count($data);
            expect($num)->toEqual(3);
            $row++;
            if ($row === 1) {
                continue;
            }
            $income = floatval($data[1]);
            $x[] = $income;
            $happiness = floatval($data[2]);
            $y[] = $happiness;
            expect($income)->toBeFloat();
            expect($income)->toBeGreaterThan(0);
            expect($happiness)->toBeFloat();
        }
        list($slope, $intercept) = Stat::linearRegression($x, $y);
        expect(round($slope, 5))->toEqual(0.71383);
        expect(round($intercept, 5))->toEqual(0.20427);
        //expect(round(Stat::median($x), 5))->toEqual(0);

        fclose($handle);
    }

    expect($row)->toEqual(499);
});
