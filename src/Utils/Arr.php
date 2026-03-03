<?php

namespace HiFolks\Statistics\Utils;

class Arr
{
    /**
     * Returns a string with values joined with a separator.
     *
     * @param  mixed[]  $data
     */
    public static function toString(array $data, bool|int $sample = false): string
    {
        if ($sample) {
            return implode(',', array_slice($data, 0, (int) $sample));
        }

        return implode(',', $data);
    }

    /**
     * Eliminate 0 value from the array.
     *
     * @param  mixed[]  $data
     * @return mixed[]
     */
    public static function stripZeroes(array $data): array
    {
        $del_val = 0;

        return array_values(array_filter($data, fn($e): bool => $e != $del_val));
    }

    /**
     * Extract one or more columns from an array of associative arrays.
     *
     * Returns one array per requested column, in the same order as $columns.
     *
     * Example:
     *   [$finishTimes, $ages] = Arr::extract($runners, ['finish', 'age']);
     *
     * @param  array<array<string, mixed>>  $data
     * @param  string[]  $columns
     * @return array<array<mixed>>
     */
    public static function extract(array $data, array $columns): array
    {
        $result = [];
        foreach ($columns as $column) {
            $result[] = array_column($data, $column);
        }

        return $result;
    }

    /**
     * Partition an array of associative arrays into two groups based on a condition.
     *
     * Returns [$matching, $nonMatching] — both groups contain full rows.
     * Supported operators: ==, !=, >, <, >=, <=
     *
     * Example:
     *   [$menRunners, $womenRunners] = Arr::partition($runners, 'gender', '==', 'M');
     *
     * @param  array<array<string, mixed>>  $data
     * @param  string  $operator  one of ==, !=, >, <, >=, <=
     * @return array{0: array<array<string, mixed>>, 1: array<array<string, mixed>>}
     */
    public static function partition(array $data, string $field, string $operator, mixed $value): array
    {
        $matching = [];
        $nonMatching = [];

        foreach ($data as $row) {
            $fieldValue = $row[$field] ?? null;

            if (self::compare($fieldValue, $operator, $value)) {
                $matching[] = $row;
            } else {
                $nonMatching[] = $row;
            }
        }

        return [$matching, $nonMatching];
    }

    private static function compare(mixed $fieldValue, string $operator, mixed $value): bool
    {
        return match ($operator) {
            '==' => $fieldValue == $value,
            '!=' => $fieldValue != $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            default => false,
        };
    }
}
