<?php

namespace HiFolks\Statistics;

class ArrUtil
{
    /**
     * Returns a string with values joined with a separator
     *
     * @param  mixed[]  $data
     */
    public static function toString(array $data, bool|int $sample = false): string
    {
        if ($sample) {
            return implode(',', array_slice($data, 0, intval($sample)));
        }

        return implode(',', $data);
    }

    /**
     * Eliminate 0 value from the array
     *
     * @param  mixed[]  $data
     * @return mixed[]
     */
    public static function stripZeroes(array $data): array
    {
        $del_val = 0;

        return array_values(array_filter($data, fn ($e) => $e != $del_val));
    }
}
