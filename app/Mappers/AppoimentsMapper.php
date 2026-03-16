<?php

namespace App\Mappers;

use App\Dtos\CreateCitasDto;
use App\Models\ScheduleModel;
use App\utils\DateManager;
use Carbon\Carbon;
use stdClass;


class AppoimentsMapper
{
    public static function mapAppoimentToClone(stdClass $appoiment, string $usuario, Carbon $dateNewAppoiment, string $cedulaUsuario): array
    {
        return [
            'nro_hist'         => $appoiment->nro_hist,
            'cedprof'          => $appoiment->cedprof,
            'ced_usu'          => $cedulaUsuario,
            'registro'         => $usuario,
            'sede'             => $appoiment->sede,
            'observaciones_mc' => $appoiment->observaciones_mc,
            'codent'           => $appoiment->codent,
            'codent2'          => $appoiment->codent2,
            'tiempo'           => $appoiment->tiempo,
            'direccion_cita'   => $appoiment->direccion_cita,
            'procedim'         => $appoiment->procedim,
            'procedipro'       => $appoiment->procedipro,
            'autoriz'          => $appoiment->autoriz,
            'fecha'            => $dateNewAppoiment->format('Y-m-d'),
            'hora'             => $appoiment->hora,
            'fec_hora'         => Carbon::now()->format('Y-m-d H:i:s'),
            'recordatorio_wsp' => $appoiment->recordatorio_wsp,
            'copago'           => $appoiment->copago??0,
        ];
    }
    public static function AppoDtoToAppo(CreateCitasDto $dto,Carbon $session,ScheduleModel $schedule){
        
        $sessionCopy=$session->copy();
        $hour=DateManager::getHoursOfDateInAmPmFormat($sessionCopy);
        $dateAppo=$sessionCopy->format('Y-m-d');
        $CreationDate=Carbon::now()->format('Y-m-d H:i:s');

        $dto->setDate($dateAppo);
        $dto->setHour($hour);
        $dto->setDateCreation($CreationDate);
        $dto->setHeadQuarter($schedule->getCodHeadQuarter());
        $dto->setDirection($schedule->getDirection());
    }
}
