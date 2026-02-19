<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Stat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StatDatasetTest extends TestCase
{
    public function test_mean(): void
    {
        $this->assertEquals(2.8, Stat::mean([1, 2, 3, 4, 4]));
        $this->assertEquals(2.625, Stat::mean([-1.0, 2.5, 3.25, 5.75]));
    }

    public function test_mean_chain(): void
    {
        $this->assertEquals(2.8, Stat::mean([1, 2, 3, 4, 4]));
        $this->assertEquals(2.625, Stat::mean([-1.0, 2.5, 3.25, 5.75]));
    }

    /** @param array<int|float> $input */
    #[DataProvider('meanDatasetProvider')]
    public function test_mean_dataset(array $input, float $result): void
    {
        $this->assertEquals($result, Stat::mean($input));
    }

    /** @return array<array{array<int|float>, float}> */
    public static function meanDatasetProvider(): array
    {
        return [
            [[1, 2, 3, 4, 4], 2.8],
            [[-1.0, 2.5, 3.25, 5.75], 2.625],
        ];
    }

    /** @param array<int|float> $input */
    #[DataProvider('dynamicOperationProvider')]
    public function test_dynamic_operation(string $methodName, array $input, float $result): void
    {
        $this->assertEquals($result, Stat::$methodName($input));
    }

    /** @return array<array{string, array<int|float>, float}> */
    public static function dynamicOperationProvider(): array
    {
        return [
            ['mean', [1, 2, 3, 4, 4], 2.8],
            ['mean', [-1.0, 2.5, 3.25, 5.75], 2.625],
            ['median', [1, 3, 5], 3],
            ['median', [1, 3, 5, 7], 4],
            ['medianLow', [1, 3, 5], 3],
            ['medianLow', [1, 3, 5, 7], 3],
        ];
    }

    /** @param array<int|float> $input */
    #[DataProvider('externalDatasetProvider')]
    public function test_dynamic_operation_with_external_dataset(string $methodName, array $input, float $result): void
    {
        $this->assertEquals(
            $result,
            Stat::$methodName($input),
        );

        $this->assertEquals(
            $result,
            Stat::$methodName($input),
        );
    }

    /** @return array<array{string, array<int|float>, float}> */
    public static function externalDatasetProvider(): array
    {
        return [
            ['mean', [1, 2, 3, 4, 4], 2.8],
            ['mean', [-1.0, 2.5, 3.25, 5.75], 2.625],
            ['median', [1, 3, 5], 3],
            ['median', [1, 3, 5, 7], 4],
            ['medianLow', [1, 3, 5], 3],
            ['medianLow', [1, 3, 5, 7], 3],
        ];
    }
}
