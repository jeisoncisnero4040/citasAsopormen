<?php

namespace App\Mappers;

use Carbon\Carbon;
use App\utils\DateManager;

class AuditMappers
{
    public static function mapDatesToGetAudit($data)
    {   
        if($data==null || empty($data)){
            return [];
        }
        if (isset($data['from']) && $data['from']) {
            $data['from'] = Carbon::parse($data['from'])->format('Y-m-d H:i:s');
        }

        if (isset($data['to']) && $data['to']) {
            $data['to'] = Carbon::parse($data['to'])->format('Y-m-d H:i:s');
        }

        return $data;
    }

    public static function mapRegisterFromModules($data)
    {
        $action = $data['action'];
        switch ($action) {
            case 'create_citas':
                return self::createNewCitasAudit($data);
            case 'eliminar_cita':
                return self::deleteCitaRegister($data);
            case 'cancelar_cita':
                return self::cancelCita($data);
            case 'cambiar_profesional':
                return self::changeProfesional($data);
            default:
                return ['description' => 'Acción no reconocida', 'modulo' => 'desconocido'];
        }
    }

    private static function createNewCitasAudit($data)
    {
        $ids = $data["ids"];
        $numCitas = count($ids);
        $idsStr = implode(', ', $ids);
        $dateRange = $data['range'];
        $start = DateManager::dateToStringFormat(Carbon::parse($dateRange[0]));
        $end = DateManager::dateToStringFormat(Carbon::parse($dateRange[1]));
        $now = DateManager::dateToStringFormat(Carbon::now());

        $usuario = $data['usuario'];
        $cliente = $data['cliente'];
        $profesional = $data['profesional'];

        $description = "El usuario {$usuario} creó un total de {$numCitas} cita(s) desde el día {$start} hasta el día {$end} para el cliente {$cliente}, asignadas al profesional {$profesional} el día {$now}. IDs: {$idsStr}";

        return ['descripcion' =>strtolower( $description), 'modulo' => 'citas'];
    }

    private static function deleteCitaRegister($data)
    {   
        $clientOrProfesional=$data['profesional']?'profesional':'cliente';
        $id = $data['id'];
        $usuario = $data['usuario'];
        $profesional = $data['profesional']??$data['cliente'];
        $now = DateManager::dateToStringFormat(Carbon::now());

        $description = "El usuario {$usuario} eliminó la cita con ID {$id} asignada al {$clientOrProfesional} {$profesional} el día {$now}.";

        return ['descripcion' =>strtolower( $description), 'modulo' => 'citas'];
    }

    private static function cancelCita($data)
    {
        $ids = str_replace('|||',', ',$data['ids']);
        $usuario = $data['usuario'];
        $cliente = $data['cliente'];
        $now = DateManager::dateToStringFormat(Carbon::now());

        $description = "El usuario {$usuario} canceló la cita con IDs {$ids} del cliente {$cliente} el día {$now}.";

        return ['descripcion' =>strtolower( $description), 'modulo' => 'citas'];
    }

    private static function changeProfesional($data)
    {
        $fromProfesional = $data['profesional_1'];
        $toProfesional = $data['profesional_2'];
        $usuario = $data['usuario'];
        $now = DateManager::dateToStringFormat(Carbon::now());
        $ids = implode(', ', $data["ids"]);

        $description = "El usuario {$usuario} reasignó las citas con IDs {$ids} del profesional {$fromProfesional} al profesional {$toProfesional} el día {$now}.";

        return ['descripcion' =>strtolower( $description), 'modulo' => 'citas'];
    }
}
