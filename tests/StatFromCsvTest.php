<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Stat;
use PHPUnit\Framework\TestCase;

class StatFromCsvTest extends TestCase
{
    public function test_parse_csv(): void
    {
        $row = 0;

        if (($handle = fopen(getcwd() . '/tests/data/income.data.csv', 'r')) !== false) {
            $x = [];
            $y = [];
            while (($data = fgetcsv(
                $handle,
                1000,
                separator: ',',
                enclosure: '"',
                escape: "",
            )) !== false) {
                $num = count($data);
                $this->assertEquals(3, $num);
                $row++;
                if ($row === 1) {
                    continue;
                }
                $income = floatval($data[1]);
                $x[] = $income;
                $happiness = floatval($data[2]);
                $y[] = $happiness;
                $this->assertIsFloat($income);
                $this->assertGreaterThan(0, $income);
                $this->assertIsFloat($happiness);
            }
            [$slope, $intercept] = Stat::linearRegression($x, $y);
            $this->assertEquals(0.71383, round($slope, 5));
            $this->assertEquals(0.20427, round($intercept, 5));

            fclose($handle);
        }

        $this->assertEquals(499, $row);
    }
}
