<?php

namespace App\Utils;
use Carbon\Carbon;

class DateManager{
    public static function nowInLargeFormat(){
        Carbon::setLocale('es');
        return Carbon::now()->translatedFormat('j \d\e F \d\e\ Y \a \l\a\s g:i');

    }
}