<?php

namespace HiFolks\Statistics\Utils;

class Format
{
    /**
     * Convert seconds to an associative array with hours, minutes, and seconds.
     *
     * @return array{hours: int, minutes: int, seconds: int}
     */
    public static function secondsToHms(int|float $seconds): array
    {
        $totalSeconds = (int) $seconds;
        $h = intdiv($totalSeconds, 3600);
        $m = intdiv($totalSeconds % 3600, 60);
        $s = $totalSeconds % 60;

        return ['hours' => $h, 'minutes' => $m, 'seconds' => $s];
    }

    /**
     * Convert hours, minutes, and seconds to total seconds.
     */
    public static function hmsToSeconds(int $hours, int $minutes, int $seconds): int
    {
        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    /**
     * Convert seconds to a human-readable time string (e.g. "1:20:45").
     */
    public static function secondsToTime(int|float $seconds): string
    {
        $hms = self::secondsToHms($seconds);

        return sprintf('%d:%02d:%02d', $hms['hours'], $hms['minutes'], $hms['seconds']);
    }

    /**
     * Parse a time string (e.g. "01:20:45" or "1:20:45") to total seconds.
     */
    public static function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);

        if (count($parts) !== 3) {
            throw new \InvalidArgumentException(
                "Invalid time format '{$time}'. Expected format: H:MM:SS or HH:MM:SS"
            );
        }

        return self::hmsToSeconds((int) $parts[0], (int) $parts[1], (int) $parts[2]);
    }
}
