<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Utils\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function test_extract_single_column(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        [$names] = Arr::extract($data, ['name']);
        $this->assertSame(['Alice', 'Bob'], $names);
    }

    public function test_extract_multiple_columns(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 30, 'score' => 95],
            ['name' => 'Bob', 'age' => 25, 'score' => 87],
        ];

        [$ages, $scores] = Arr::extract($data, ['age', 'score']);
        $this->assertSame([30, 25], $ages);
        $this->assertSame([95, 87], $scores);
    }

    public function test_extract_empty_array(): void
    {
        $result = Arr::extract([], ['name']);
        $this->assertSame([[]], $result);
    }

    public function test_partition_equals(): void
    {
        $data = [
            ['gender' => 'M', 'time' => 100],
            ['gender' => 'F', 'time' => 110],
            ['gender' => 'M', 'time' => 105],
        ];

        [$men, $women] = Arr::partition($data, 'gender', '==', 'M');
        $this->assertCount(2, $men);
        $this->assertCount(1, $women);
        $this->assertSame('F', $women[0]['gender']);
    }

    public function test_partition_not_equals(): void
    {
        $data = [
            ['status' => 'active'],
            ['status' => 'inactive'],
            ['status' => 'active'],
        ];

        [$nonActive, $active] = Arr::partition($data, 'status', '!=', 'active');
        $this->assertCount(1, $nonActive);
        $this->assertCount(2, $active);
    }

    public function test_partition_greater_than(): void
    {
        $data = [
            ['age' => 30],
            ['age' => 20],
            ['age' => 40],
        ];

        [$older, $younger] = Arr::partition($data, 'age', '>', 25);
        $this->assertCount(2, $older);
        $this->assertCount(1, $younger);
    }

    public function test_partition_less_than(): void
    {
        $data = [
            ['score' => 50],
            ['score' => 80],
            ['score' => 30],
        ];

        [$low, $high] = Arr::partition($data, 'score', '<', 60);
        $this->assertCount(2, $low);
        $this->assertCount(1, $high);
    }

    public function test_partition_greater_than_or_equal(): void
    {
        $data = [
            ['value' => 10],
            ['value' => 20],
            ['value' => 20],
        ];

        [$matching, $nonMatching] = Arr::partition($data, 'value', '>=', 20);
        $this->assertCount(2, $matching);
        $this->assertCount(1, $nonMatching);
    }

    public function test_partition_less_than_or_equal(): void
    {
        $data = [
            ['value' => 10],
            ['value' => 20],
            ['value' => 30],
        ];

        [$matching, $nonMatching] = Arr::partition($data, 'value', '<=', 20);
        $this->assertCount(2, $matching);
        $this->assertCount(1, $nonMatching);
    }

    public function test_partition_empty_array(): void
    {
        [$matching, $nonMatching] = Arr::partition([], 'field', '==', 'value');
        $this->assertSame([], $matching);
        $this->assertSame([], $nonMatching);
    }

    public function test_partition_preserves_full_rows(): void
    {
        $data = [
            ['name' => 'Alice', 'age' => 30, 'city' => 'NYC'],
        ];

        [$matching, $nonMatching] = Arr::partition($data, 'age', '==', 30);
        $this->assertSame($data[0], $matching[0]);
    }

    public function test_partition_invalid_operator(): void
    {
        $data = [['value' => 10]];

        [$matching, $nonMatching] = Arr::partition($data, 'value', '===', 10);
        $this->assertCount(0, $matching);
        $this->assertCount(1, $nonMatching);
    }

    public function test_to_string(): void
    {
        $this->assertSame('1,2,3', Arr::toString([1, 2, 3]));
    }

    public function test_to_string_with_sample(): void
    {
        $this->assertSame('1,2', Arr::toString([1, 2, 3, 4], 2));
    }

    public function test_strip_zeroes(): void
    {
        $this->assertSame([1, 2, 3], Arr::stripZeroes([0, 1, 0, 2, 3, 0]));
    }
}
