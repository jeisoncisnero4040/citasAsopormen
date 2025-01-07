<?php

namespace App\utils;

 
use Spatie\Holidays\Holidays;
use Carbon\Carbon;
use App\Exceptions\CustomExceptions\ServerErrorException;

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
    public static function ConvertHourTo24Format($hour) {
        try {
            return Carbon::createFromFormat('g:i a', $hour)->format('H:i');
        } catch (\Exception $e) {
            throw new ServerErrorException('Formato de hora inválido. Usa el formato "g:i a"', 500);
        }
    }

    public static function CalculateMinutesSinceStartOfDay($hour24) {
        try {
            return Carbon::createFromFormat('H:i', '00:00')->diffInMinutes(Carbon::createFromFormat('H:i', $hour24));
        } catch (\Exception $e) {
            throw new ServerErrorException('Error al calcular los minutos desde el inicio del día', 500);
        }
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
