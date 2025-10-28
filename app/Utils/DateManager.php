<?php
namespace App\Utils;

use Carbon\Carbon;
use App\Exceptions\CustomExceptions\ServerErrorException;

class DateManager {

    static public $daysWeek = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];

    /**
     * Devuelve la fecha en formato corto: "Día/Mes Hora AM/PM"
     * 
     * @param mixed $date (string, DateTime o Carbon)
     * @return string
     * @throws ServerErrorException Si la fecha no es válida
     */
    public static function getDshrtDate($date) {
        try {
            $date = self::parseDate($date);
            return $date->format('d/m h:i A');
        } catch (\Exception $e) {
            throw new ServerErrorException("Fecha inválida proporcionada.");
        }
    }

    /**
     * Devuelve el nombre del día de la semana para una fecha dada.
     * 
     * @param mixed $date (string, DateTime o Carbon)
     * @return string
     * @throws ServerErrorException Si la fecha no es válida
     */
    public static function getDayWeekToDate($date) {
        try {
            $date = self::parseDate($date);
            return self::$daysWeek[$date->dayOfWeek];
        } catch (\Exception $e) {
            throw new ServerErrorException("Fecha inválida proporcionada.");
        }
    }

    /**
     * Convierte una fecha en un objeto Carbon, sin importar el tipo de entrada.
     * 
     * @param mixed $date (string, DateTime o Carbon)
     * @return Carbon
     * @throws \Exception Si la fecha no es válida
     */
    private static function parseDate($date) {
        if (!$date instanceof Carbon) {
            return Carbon::parse($date);
        }
        return $date;
    }
}
