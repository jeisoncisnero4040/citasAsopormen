<?php

namespace App\utils;

class NumDocsHelper
{

    public static function calculateDV(string|int $nit): int
    {
        $nit = (string) $nit;

        $weights = [
            3,7,13,17,19,23,29,37,41,43,47,53,59,67,71
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