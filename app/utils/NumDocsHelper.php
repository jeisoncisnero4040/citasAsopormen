<?php

namespace App\utils;

class NumDocsHelper
{

    public static function calculateDV(string|int $nit): int
    {
        $nit = (string) $nit;

        $weights = [
            71,67,59,53,47,43,41,37,29,23,19,17,13,7,3
        ];

        $nitLength = strlen($nit);
        $sum = 0;

        for ($i = 0; $i < $nitLength; $i++) {
            $digit = (int) $nit[$nitLength - $i - 1];
            $sum += $digit * $weights[$i];
        }

        $remainder = $sum % 11;

        if ($remainder > 1) {
            return 11 - $remainder;
        }

        return $remainder;
    }

}