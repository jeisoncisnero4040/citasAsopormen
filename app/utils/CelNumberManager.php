<?php

namespace App\utils;

class CelNumberManager {


        
    public static function chooseTelephoneNumber(string $telephoneNumbers){

        $telephoneWithoutIndicative = str_replace('+57', '', $telephoneNumbers);
        $telephoneNumbersOnly = preg_replace('/\D/', '', $telephoneWithoutIndicative);
        if (strlen($telephoneNumbersOnly) < 10) {
            return null;
        }
        $firstTelephoneNumber = substr($telephoneNumbersOnly, 0, 10);
        if (str_starts_with($firstTelephoneNumber, '3')) {
            return $firstTelephoneNumber;
        }

        if (strlen($telephoneNumbersOnly) >= 20 && strlen($telephoneNumbersOnly) % 10 == 0) {
            $secondTelephoneNumber = substr($telephoneNumbersOnly, 10, 10);
            if (str_starts_with($secondTelephoneNumber, '3')) {
                return $secondTelephoneNumber;
            }
        }
        return substr($telephoneNumbersOnly, -10);
    }
}
    


