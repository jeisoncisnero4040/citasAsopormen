<?php
namespace App\Utils;

use Carbon\Carbon;

class DateManager extends Carbon{
    static public $daysWeek = [
        0 => 'domingo',    
        1 => 'lunes',    
        2 => 'martes',     
        3 => 'miercoles',  
        4 => 'jueves',     
        5 => 'viernes',    
        6 => 'sabado'      
    ];
    static public $daysWeekInverted=[
        'domingo'=>7,
        'lunes'=>1,
        'martes'=>2,
        'miercoles'=>3,
        'jueves'=>4,
        'viernes'=>5,
        'sabado'=>6
    ];
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
    public static function dateToStringFormat(string $dateStr) {
        $date=self::parse($dateStr);
        $month = self::getMonthToDate($date);
        $dayOfWeek = self::getDayWeekToDate($date);
        $day = $date->day;
        $year = $date->year; 

        return "$dayOfWeek $day de $month de $year";
    }
    private static function getMonthToDate(Carbon $date) {
        $month = str_pad($date->month, 2, '0', STR_PAD_LEFT);
        return self::$monthsList[$month];
    }

    private static function getDayWeekToDate(Carbon $date) {
        $day = $date->dayOfWeek; 
        return self::$daysWeek[$day];
    }
}