<?php

namespace HiFolks\Statistics;

class Math
{
    /**
     * Rounds value with the given precision, if the round is not null.
     *
     * @param  float  $value
     * @param  int|null  $round
     * @return float
     */
    public static function round(float $value, ?int $round): float
    {
        return is_null($round) ? $value : round($value, $round);
    }

    /**
     * Check if number is odd.
     *
     * @param  int  $number
     * @return bool
     */
    public static function isOdd(int $number): bool
    {
        return (bool) ($number & 1);
    }
}
