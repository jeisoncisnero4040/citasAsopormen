<?php

namespace App\Utils;

use Carbon\Carbon;

class DateManager {

    static private $monthsList = [
        '01' => 'enero',
        '02' => 'febrero',
        '03' => 'marzo',
        '04' => 'abril',
        '05' => 'mayo',
        '06' => 'junio',
        '07' => 'julio',
        '08' => 'agosto',
        '09' => 'septiembre',
        '10' => 'octubre',
        '11' => 'noviembre',
        '12' => 'diciembre',
    ];

    static private $daysList = [
        0 => 'Domingo',    
        1 => 'Lunes',    
        2 => 'Martes',     
        3 => 'Miércoles',  
        4 => 'Jueves',     
        5 => 'Viernes',    
        6 => 'Sábado',      
    ];

    public static function dateToStringFormat(Carbon $date) {
        $month = self::getMonthToDate($date);
        $dayOfWeek = self::getDayWeekToDate($date);
        $day = $date->day;
        $hour = self::getHoursOfDateInAmPmFormat($date);

         
        if ($date->isTomorrow()) {
            $dateInStringFormat = "Mañana a las $hour";
        } else {
            $dateInStringFormat = "$dayOfWeek $day de $month a las $hour";
        }

        return $dateInStringFormat;
    }

    private static function getMonthToDate(Carbon $date) {
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        return self::$monthsList[$month];
    }

    private static function getDayWeekToDate(Carbon $date) {
        $day = $date->dayOfWeek; 
        return self::$daysList[$day];
    }

    private static function getHoursOfDateInAmPmFormat(Carbon $date) {
        $hour = $date->hour;
        $minute = $date->minute;

        $period = $hour < 12 ? 'AM' : 'PM';  
        $hour = $hour % 12;
        $hour = $hour ? $hour : 12; 
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);  
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);

        return "$hour:$minute $period";
    }
}

 