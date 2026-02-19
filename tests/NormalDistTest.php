<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\NormalDist;
use PHPUnit\Framework\TestCase;

class NormalDistTest extends TestCase
{
    public function test_init_normal_dist(): void
    {
        $nd = new NormalDist(1060, 195);
        $this->assertEquals(1060, $nd->getMean());
        $this->assertEquals(195, $nd->getSigma());
    }

    public function test_can_calculate_normal_dist_cdf(): void
    {
        $nd = new NormalDist(1060, 195);
        $this->assertEquals(0.184, round($nd->cdf(1200 + 0.5) - $nd->cdf(1100 - 0.5), 3));
    }

    public function test_can_calculate_normal_dist_pdf(): void
    {
        $nd = new NormalDist(10, 2);
        $this->assertEquals(0.121, $nd->pdfRounded(12, 3));
        $this->assertEquals(0.12, $nd->pdfRounded(12, 2));
    }

    public function test_load_normal_dist_from_samples(): void
    {
        $samples = [2.5, 3.1, 2.1, 2.4, 2.7, 3.5];
        $normalDist = NormalDist::fromSamples($samples);
        $this->assertEquals(2.71667, $normalDist->getMeanRounded(5));
        $this->assertEquals(0.50761, $normalDist->getSigmaRounded(5));
    }

    public function test_add_to_normal_dist(): void
    {
        $birth_weights = NormalDist::fromSamples([2.5, 3.1, 2.1, 2.4, 2.7, 3.5]);
        $drug_effects = new NormalDist(0.4, 0.15);
        $combined = $birth_weights->add($drug_effects);
        $this->assertEquals(3.1, $combined->getMeanRounded(1));
        $this->assertEquals(0.5, $combined->getSigmaRounded(1));
        $this->assertEquals(2.71667, $birth_weights->getMeanRounded(5));
        $this->assertEquals(0.50761, $birth_weights->getSigmaRounded(5));
    }

    public function test_multiply_normal_dist(): void
    {
        $tempFebruaryCelsius = new NormalDist(5, 2.5);
        $tempFebFahrenheit = $tempFebruaryCelsius->multiply(9 / 5)->add(32);
        $this->assertEquals(41.0, $tempFebFahrenheit->getMeanRounded(1));
        $this->assertEquals(4.5, $tempFebFahrenheit->getSigmaRounded(1));
        $this->assertEquals(5.0, $tempFebruaryCelsius->getMeanRounded(1));
        $this->assertEquals(2.5, $tempFebruaryCelsius->getSigmaRounded(1));
    }
}
