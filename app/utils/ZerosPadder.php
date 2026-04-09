<?php

namespace App\utils;

class ZerosPadder
{
    public static function apply(string $value, int $length): string
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT);
    }

    public static function increment(string $value, int $length = 10): string
    {
        $intValue = (int) $value;
        $incrementedValue = $intValue + 1;

        return str_pad((string) $incrementedValue, $length, '0', STR_PAD_LEFT);
    }
}