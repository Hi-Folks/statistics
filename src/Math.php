<?php

namespace HiFolks\Statistics;

class Math
{
    /**
     * @param float $value
     * @param int|null $round
     * @return float
     */
    public static function round(float $value, ?int $round): float
    {
        return is_null($round) ? $value : round($value, $round);
    }

    /**
     * @param int $number
     * @return bool
     */
    public static function isOdd(int $number): bool
    {
        return ($number % 2) == 1;
    }
}
