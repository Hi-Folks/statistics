<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Freq;
use PHPUnit\Framework\TestCase;

class FreqTest extends TestCase
{
    public function test_can_calculate_freq_table(): void
    {
        $this->assertEquals([4 => 2, 3 => 1, 1 => 1, 2 => 1], Freq::frequencies([1, 2, 3, 4, 4]));
        $this->assertEquals([], Freq::frequencies([]));

        $result = Freq::frequencies(['red', 'blue', 'blue', 'red', 'green', 'red', 'red']);
        $this->assertEquals(['red' => 4, 'blue' => 2, 'green' => 1], $result);
        $this->assertCount(3, $result);
        $this->assertEquals(4, $result['red']);
        $this->assertEquals(2, $result['blue']);
        $this->assertEquals(1, $result['green']);

        $result = Freq::frequencies([2.1, 2.7, 1.4, 2.45], true);
        $this->assertEquals([2 => 3, 1 => 1], $result);
        $this->assertCount(2, $result);
    }

    public function test_can_calculate_relative_freq_table(): void
    {
        $this->assertEquals([4 => 40, 3 => 20, 1 => 20, 2 => 20], Freq::relativeFrequencies([1, 2, 3, 4, 4]));
        $this->assertEquals([], Freq::relativeFrequencies([]));

        $result = Freq::relativeFrequencies(['red', 'blue', 'blue', 'red', 'green', 'red', 'red'], 2);
        $this->assertEquals(['red' => 57.14, 'blue' => 28.57, 'green' => 14.29], $result);
        $this->assertCount(3, $result);
        $this->assertEquals(57.14, $result['red']);
        $this->assertEquals(28.57, $result['blue']);
        $this->assertEquals(14.29, $result['green']);

        $result = Freq::relativeFrequencies([2.1, 2.7, 1.4, 2.45], 1);
        $this->assertEquals([2 => 75, 1 => 25], $result);
        $this->assertCount(2, $result);
    }

    public function test_can_calculate_grouped_frequency_table(): void
    {
        $data = [1, 1, 1, 4, 4, 5, 5, 5, 6, 7, 8, 8, 8, 9, 9, 9, 9, 9, 9, 10, 10, 11, 12, 12,
            13, 14, 14, 15, 15, 16, 16, 16, 16, 17, 17, 17, 18, 18, ];

        $table = Freq::frequencyTable($data, 7);
        $this->assertCount(6, $table);
        $this->assertEquals([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTable($data, 6);
        $this->assertCount(6, $table);
        $this->assertEquals([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTable($data, 8);
        $this->assertCount(6, $table);
        $this->assertEquals([1 => 3, 4 => 6, 7 => 10, 10 => 5, 13 => 5, 16 => 9], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTable($data, 3);
        $this->assertCount(3, $table);
        $this->assertEquals([1 => 9, 7 => 15, 13 => 14], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTable($data);
        $this->assertCount(18, $table);
        $this->assertEquals([1 => 3, 2 => 0, 3 => 0, 4 => 2, 5 => 3, 6 => 1, 7 => 1, 8 => 3, 9 => 6,
            10 => 2, 11 => 1, 12 => 2, 13 => 1, 14 => 2, 15 => 2, 16 => 4, 17 => 3, 18 => 2, ], $table);
    }

    public function test_can_calculate_grouped_frequency_table_by_size(): void
    {
        $data = [1, 1, 1, 4, 4, 5, 5, 5, 6, 7, 8, 8, 8, 9, 9, 9, 9, 9, 9, 10, 10, 11, 12, 12,
            13, 14, 14, 15, 15, 16, 16, 16, 16, 17, 17, 17, 18, 18, ];

        $table = Freq::frequencyTableBySize($data, 4);
        $this->assertCount(5, $table);
        $this->assertEquals([1 => 5, 5 => 8, 9 => 11, 13 => 9, 17 => 5], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTableBySize($data, 5);
        $this->assertCount(4, $table);
        $this->assertEquals([1 => 8, 6 => 13, 11 => 8, 16 => 9], $table);
        $this->assertEquals(count($data), array_sum($table));

        $table = Freq::frequencyTableBySize($data, 8);
        $this->assertCount(3, $table);
        $this->assertEquals([1 => 13, 9 => 20, 17 => 5], $table);
        $this->assertEquals(count($data), array_sum($table));
    }
}
