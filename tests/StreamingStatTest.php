<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Exception\InvalidDataInputException;
use HiFolks\Statistics\Stat;
use HiFolks\Statistics\StreamingStat;
use PHPUnit\Framework\TestCase;

class StreamingStatTest extends TestCase
{
    private const TOLERANCE = 1e-10;

    /**
     * Helper: create a StreamingStat from an array.
     */
    /** @param array<int|float> $data */
    private function fromArray(array $data): StreamingStat
    {
        $s = new StreamingStat();
        foreach ($data as $value) {
            $s->add($value);
        }

        return $s;
    }

    public function test_matches_stat_mean(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::mean($data), $s->mean(), self::TOLERANCE);
    }

    public function test_matches_stat_variance(): void
    {
        $data = [2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::variance($data), $s->variance(), self::TOLERANCE);
    }

    public function test_matches_stat_pvariance(): void
    {
        $data = [0.0, 0.25, 0.25, 1.25, 1.5, 1.75, 2.75, 3.25];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::pvariance($data), $s->pvariance(), self::TOLERANCE);
    }

    public function test_matches_stat_stdev(): void
    {
        $data = [1.5, 2.5, 2.5, 2.75, 3.25, 4.75];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::stdev($data), $s->stdev(), self::TOLERANCE);
    }

    public function test_matches_stat_pstdev(): void
    {
        $data = [1.5, 2.5, 2.5, 2.75, 3.25, 4.75];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::pstdev($data), $s->pstdev(), self::TOLERANCE);
    }

    public function test_matches_stat_skewness(): void
    {
        $data = [2, 8, 0, 4, 1, 9, 9, 0];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::skewness($data), $s->skewness(), self::TOLERANCE);
    }

    public function test_matches_stat_pskewness(): void
    {
        $data = [2, 8, 0, 4, 1, 9, 9, 0];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::pskewness($data), $s->pskewness(), self::TOLERANCE);
    }

    public function test_matches_stat_kurtosis(): void
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::kurtosis($data), $s->kurtosis(), self::TOLERANCE);
    }

    public function test_rounding(): void
    {
        $data = [2.75, 1.75, 1.25, 0.25, 0.5, 1.25, 3.5];
        $s = $this->fromArray($data);

        $this->assertEquals(
            round(Stat::variance($data), 4),
            $s->variance(4),
        );
        $this->assertEquals(
            round((float) Stat::mean($data), 2),
            $s->mean(2),
        );
    }

    public function test_chaining(): void
    {
        $s = (new StreamingStat())->add(1)->add(2)->add(3);
        $this->assertEquals(3, $s->count());
        $this->assertEqualsWithDelta(2.0, $s->mean(), self::TOLERANCE);
    }

    public function test_empty_mean_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        (new StreamingStat())->mean();
    }

    public function test_one_element_variance_throws(): void
    {
        $s = (new StreamingStat())->add(5);
        $this->assertEqualsWithDelta(5.0, $s->mean(), self::TOLERANCE);

        $this->expectException(InvalidDataInputException::class);
        $s->variance();
    }

    public function test_two_elements_skewness_throws(): void
    {
        $s = (new StreamingStat())->add(1)->add(2);
        // variance should work
        $this->assertEqualsWithDelta(Stat::variance([1, 2]), $s->variance(), self::TOLERANCE);

        $this->expectException(InvalidDataInputException::class);
        $s->skewness();
    }

    public function test_three_elements_kurtosis_throws(): void
    {
        $s = (new StreamingStat())->add(1)->add(2)->add(3);
        // skewness should work
        $this->assertEqualsWithDelta(Stat::skewness([1, 2, 3]), $s->skewness(), self::TOLERANCE);

        $this->expectException(InvalidDataInputException::class);
        $s->kurtosis();
    }

    public function test_insufficient_data_pskewness_throws(): void
    {
        $s = (new StreamingStat())->add(1)->add(2);
        $this->expectException(InvalidDataInputException::class);
        $s->pskewness();
    }

    public function test_identical_values_pskewness_throws(): void
    {
        $s = $this->fromArray([5, 5, 5, 5]);
        $this->expectException(InvalidDataInputException::class);
        $s->pskewness();
    }

    public function test_identical_values_skewness_throws(): void
    {
        $s = $this->fromArray([5, 5, 5, 5]);
        $this->expectException(InvalidDataInputException::class);
        $s->skewness();
    }

    public function test_identical_values_kurtosis_throws(): void
    {
        $s = $this->fromArray([5, 5, 5, 5]);
        $this->expectException(InvalidDataInputException::class);
        $s->kurtosis();
    }

    public function test_large_dataset(): void
    {
        // Generate deterministic pseudo-random data
        mt_srand(42);
        $data = [];
        for ($i = 0; $i < 10000; $i++) {
            $data[] = mt_rand(-10000, 10000) / 100.0;
        }

        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::mean($data), $s->mean(), 1e-6);
        $this->assertEqualsWithDelta(Stat::variance($data), $s->variance(), 1e-4);
        $this->assertEqualsWithDelta(Stat::stdev($data), $s->stdev(), 1e-4);
        $this->assertEqualsWithDelta(Stat::pvariance($data), $s->pvariance(), 1e-4);
        $this->assertEqualsWithDelta(Stat::pstdev($data), $s->pstdev(), 1e-4);
        $this->assertEqualsWithDelta(Stat::skewness($data), $s->skewness(), 1e-4);
        $this->assertEqualsWithDelta(Stat::pskewness($data), $s->pskewness(), 1e-4);
        $this->assertEqualsWithDelta(Stat::kurtosis($data), $s->kurtosis(), 1e-4);
    }

    public function test_count(): void
    {
        $s = new StreamingStat();
        $this->assertEquals(0, $s->count());
        $s->add(1)->add(2);
        $this->assertEquals(2, $s->count());
    }

    public function test_negative_values(): void
    {
        $data = [-5, -3, -1, 0, 1, 3, 5];
        $s = $this->fromArray($data);

        $this->assertEqualsWithDelta(Stat::mean($data), $s->mean(), self::TOLERANCE);
        $this->assertEqualsWithDelta(Stat::variance($data), $s->variance(), self::TOLERANCE);
        $this->assertEqualsWithDelta(Stat::skewness($data), $s->skewness(), self::TOLERANCE);
    }

    public function test_pvariance_single_element(): void
    {
        $s = (new StreamingStat())->add(42);
        $this->assertEqualsWithDelta(0.0, $s->pvariance(), self::TOLERANCE);
    }

    public function test_empty_pvariance_throws(): void
    {
        $this->expectException(InvalidDataInputException::class);
        (new StreamingStat())->pvariance();
    }
}
