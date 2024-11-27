<?php

namespace App\Mappers;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Carbon;

class CalendarProfesionalMapper{
    public function map($calendar)
    {
        foreach ($calendar as &$cita) {
            
            $fechaHora = Carbon::parse($cita->fecha);
            $hora24 = $this->convertHourTo24Format($cita->hora);
            $startFechaHora = $fechaHora->copy()->addMinutes($this->calculateMinutesSinceStartOfDay($hora24));
            $minutesToAdd = $cita->duracion;
            $endFechaHora = $startFechaHora->copy()->addMinutes($minutesToAdd);
            $cita->start = $startFechaHora->toIso8601String();
            $cita->end = $endFechaHora->toIso8601String();

            if (isset($cita->usuario) && trim($cita->usuario)) {
                $cita->title = trim($cita->usuario) . ' - ' . trim($cita->procedimiento);
            }
            
        }

        return $calendar;
    }

    private function ConvertHourTo24Format($hour) {
        try {
            return Carbon::createFromFormat('g:i a', $hour)->format('H:i');
        } catch (\Exception $e) {
            throw new ServerErrorException('Formato de hora inválido. Usa el formato "g:i a"', 500);
        }
    }

    private function CalculateMinutesSinceStartOfDay($hour24) {
        try {
            return Carbon::createFromFormat('H:i', '00:00')->diffInMinutes(Carbon::createFromFormat('H:i', $hour24));
        } catch (\Exception $e) {
            throw new ServerErrorException('Error al calcular los minutos desde el inicio del día', 500);
        }
    }
}

