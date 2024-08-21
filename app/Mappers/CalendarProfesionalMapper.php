<?php

namespace App\Mappers;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Carbon;

class CalendarProfesionalMapper
{
    public function CalendarMapper($calendar)
    {
        $mappedCalendar = [];

        foreach ($calendar as $cita) {
            $fechaHora = Carbon::parse($cita->fecha);
            $hora24 = $this->ConvertHourTo24Format($cita->hora);
            $startFechaHora = $fechaHora->copy()->addMinutes($this->CalculateMinutesSinceStartOfDay($hora24));
            $minutesToAdd=$cita->duracion;
            $endFechaHora = $startFechaHora->copy()->addMinutes($minutesToAdd);

            $mappedCalendar[] = [
                'id'=>$cita->id,
                'title' => trim($cita->usuario) . ' - ' .trim($cita->procedimiento) ,
                'start' => $startFechaHora->toIso8601String(),
                'end' => $endFechaHora->toIso8601String()
            ];
        }

        return $mappedCalendar;
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

