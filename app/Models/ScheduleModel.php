<?php

namespace App\Models;

use Carbon\Carbon;
use stdClass;

class ScheduleModel
{
    private int $id;
    private string $cedula;
    private string $codHeadQuarter;
    private string $direction;
    private int $dayWeek;
    private Carbon $start;
    private Carbon $end;

    public function __construct(stdClass $schedule)
    {
        $this->id             = (int) $schedule->id;
        $this->cedula         = (string) $schedule->cedula;
        $this->codHeadQuarter = (string) $schedule->cod_sede;
        $this->direction      = (string) $schedule->direccion;
        $this->dayWeek        = (int) $schedule->dia_semana;

        $this->start = Carbon::parse($schedule->fecha_inicio);
        $this->end   = Carbon::parse($schedule->fecha_fin);
    }



    public function getId(): int
    {
        return $this->id;
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getCodHeadQuarter(): string
    {
        return $this->codHeadQuarter;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getDayWeek(): int
    {
        return $this->dayWeek;
    }

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }
    public function checkDisponibility(Carbon $date): bool
    {
        $currentMinutes = $date->hour * 60 + $date->minute;
        $startMinutes   = $this->start->hour * 60 + $this->start->minute;
        $endMinutes     = $this->end->hour * 60 + $this->end->minute;

        return
            $date->dayOfWeek === $this->dayWeek &&
            $currentMinutes >= $startMinutes &&
            $currentMinutes < $endMinutes;
    }


}
