<?php
namespace App\Mappers;

use App\Mappers\CalendarProfesionalMapper;
use Carbon\Carbon;

class CalendarClientMapper extends CalendarProfesionalMapper
{

    public function groupCitasBySessions(array $citas, $deleteKeys = [])
    {
        $citasGroupedByDay = $this->splitCitasByDay($citas);
    
        foreach ($citasGroupedByDay as &$dayCitas) {
            $dayCitas = $this->sortCitasByStartDate($dayCitas);
    
            $processedCitas = [];
            $currentCita = clone $dayCitas[0];
            $currentCita->ids = $currentCita->id;
    
            for ($index = 1; $index < count($dayCitas); $index++) {
                $isCitaLinkedWithNext = $this->areCitasEqualAndLinked($dayCitas[$index - 1], $dayCitas[$index]);
    
                if ($isCitaLinkedWithNext) {
                    $currentCita->ids .= ' ||| ' . $dayCitas[$index]->id;
                } else {
                    $currentCita->end = $dayCitas[$index - 1]->end;
                    $processedCitas[] = $this->removeKeysFromCita($currentCita, $deleteKeys);
    
                    $currentCita = clone $dayCitas[$index];
                    $currentCita->ids = $currentCita->id;
                }
            }
    
            $currentCita->end = $dayCitas[count($dayCitas) - 1]->end;
            $processedCitas[] = $this->removeKeysFromCita($currentCita, $deleteKeys);
    
            $dayCitas = $processedCitas;
        }
    
        return $citasGroupedByDay;
    }
    
    private function removeKeysFromCita($cita, $deleteKeys)
    {
        foreach ($deleteKeys as $key) {
            if (property_exists($cita, $key)) {
                unset($cita->$key);
            }
        }
        return $cita;
    }


    private function splitCitasByDay(array $citas): array
    {
        if (empty($citas)) {
            return [];
        }

        $currentDate = $citas[0]->fecha;
        $citasGroupedByDay = [];
        $citasOfTheDay = [];

        foreach ($citas as $cita) {
            if ($this->isSameDay($currentDate, $cita->fecha)) {
                $citasOfTheDay[] = $cita;
            } else {
                $citasGroupedByDay[] = $citasOfTheDay;
                $citasOfTheDay = [$cita];
                $currentDate = $cita->fecha;
            }
        }

        // Agregar las últimas citas agrupadas.
        if (!empty($citasOfTheDay)) {
            $citasGroupedByDay[] = $citasOfTheDay;
        }

        return $citasGroupedByDay;
    }


    private function isSameDay(string $date1, string $date2): bool
    {
        return Carbon::parse($date1)->isSameDay(Carbon::parse($date2));
    }


    private function sortCitasByStartDate(array $citas): array
    {
        usort($citas, function ($a, $b) {
            return Carbon::parse($a->start)->timestamp - Carbon::parse($b->start)->timestamp;
        });

        return $citas;
    }

    private function areCitasEqualAndLinked(object $cita1, object $cita2): bool
    {
        return (
            $cita1->autorizacion === $cita2->autorizacion &&
            $cita1->tiempo === $cita2->tiempo &&
            $cita1->profesional === $cita2->profesional &&
            $cita1->end === $cita2->start
        );
    }
}
