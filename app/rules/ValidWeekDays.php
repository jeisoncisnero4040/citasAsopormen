<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidWeekDays implements Rule
{
    protected $validDays = [
        'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'
    ];

    public function passes($attribute, $value)
    {
         
        if (!is_array($value)) {
            return false;
        }
    
         
        if (empty($value)) {
            return false;
        }
    
         
        return !array_diff($value, $this->validDays);
    }
    
    public function message()
    {
        return 'El campo :attribute debe contener solo días válidos de la semana.';
    }
}
