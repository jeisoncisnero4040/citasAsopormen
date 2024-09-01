<?php

namespace App\utils;

 
use Spatie\Holidays\Holidays;
 

class DateManager {

     
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

    public static function getDayByDate($date) {

        $dayIndex = $date->dayOfWeek;
        return self::$daysWeek[$dayIndex];
    }
    public static function getHourOfDate($date) {

        return $date->hour;  
    }

    public static function getMinuteOfDate($date) {

        return $date->minute;  
    }

    public static function isHoliday($date) {

        $onlyDate=self::getDateOnly($date);
        return Holidays::for('co')->isHoliday($onlyDate);
    }
    public static function getDateOnly($date) {

        return $date->toDateString();
    }
    public static function getDateInSmallDateTime($date) {
        $date = $date->setTime(hour: 0, minute: 0);
        return $date->format('Y-m-d H:i:s');
    }
    public static function getHoursOfDateInAmPmFormat($date) {
        $hour = $date->hour;
        $minute = $date->minute;
    
        $period = $hour < 12 ? 'AM' : 'PM';
        $hour = $hour % 12;
        $hour = $hour ? $hour : 12; 
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);  
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
    
        return $hour . ':' . $minute . ' ' . $period;
    }
    
}
