<?php

namespace HiFolks\Statistics\Tests;

use HiFolks\Statistics\Math;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function test_is_odd(): void
    {
        $this->assertTrue(Math::isOdd(1));
        $this->assertFalse(Math::isOdd(0));
        $this->assertTrue(Math::isOdd(-5));
        $this->assertFalse(Math::isOdd(-2));
    }
}
