<?php

namespace App\Constants;

use Carbon\Carbon;

class AuditTemplates
{
    const LOGIN_AUDIT = "El usuario {{nombre}} ingreso al sistema el dia {{fecha}} desde la ip {{ip}}";
    const FORGOT_PASSWORD_AUDIT = "El usuario {{nombre}} solicito recuperar la contraseña por medio de la dirección electronica {{email}} el dia {{fecha}}";
    const CHANGE_PASSWORD = "El usuario {{nombre}} cambio su contraseña el día {{fecha}}";
    const CANCEL_APPOIMENTS = "El usuario {{nombre}} cancelo las citas con IDs {{ids}} pertenecientes al usuario {{usuario}} el dia {{fecha}} ";
    
    CONST CLOSE_AUTH="El usuario {{nombre}} cerro la autorizacion {{autorizacion}} con id {{id}}, Tambien las citas con id: {{ids}} del usuario {{usuario}} el dia {{fecha}}";

    CONST EVO_BASIC_TEMPLATE="El usuario {{nombre}} evoluciono la cita con ids {{ids}} pertenecientes al usuario usuario {{usuario}} el dia {{fecha}} ";

    CONST GET_EVO_TEMPLATE="EL usuario {{nombre}} consulto las evoluciones del paciente {{client}} desde las fechas {{from}} hasta {{to}} el dia {{fecha}}";
    const PRINT_EVO_TEMPLATE="El usuario {{nombre}} imprimio las evoluciones con ids {{ids}} pertenecientes al paciente {{client}}, en un rango de fechas {{from}} a {{to}} desde la direccion ip {{ip}} el dia {{fecha}}";
    const SET_AUTORIZ_APPO_TEMPLATE = "El profesional {{nombre}} cambio la autorizacion de la cita {{id}} de un valor {{old}} a {{new}} perteneciente al usuario {{usuario}} el dia {{fecha}}";


    const VARS_FORGOT_PASSWORD_AUDIT = ['{{nombre}}', '{{email}}', '{{fecha}}'];
    const VARS_LOGIN_AUDIT = ['{{nombre}}', '{{fecha}}','{{ip}}'];
    const VARS_CHANGE_PASSWORD = ['{{nombre}}', '{{fecha}}'];
    const VARS_CANCEL_APPOIMENTS = ['{{nombre}}', '{{ids}}', '{{usuario}}','{{fecha}}'];

    const VARS_BASIC_EVO=['{{nombre}}','{{ids}}','{{usuario}}','{{fecha}}'];

    const VARS_CLOSE_AUTH =['{{nombre}}','{{autorizacion}}','{{id}}','{{ids}}','{{usuario}}','{{fecha}}'];
    const VARS_GET_EVO=['{{nombre}}','{{client}}','{{from}}','{{to}}','{{fecha}}'];
    const VARS_PRINT_EVO=['{{nombre}}','{{ids}}','{{client}}','{{from}}','{{to}}','{{ip}}','{{fecha}}'];
    const VARS_SET_AUTORIZ_APPO = ['{{nombre}}','{{id}}','{{old}}','{{new}}','{{usuario}}','{{fecha}}'];

    

    

    public static function  renderEvoBasicAuditMsm(string $nombre,string $ids,string $usuario){
        return str_replace(self::VARS_BASIC_EVO,[
            $nombre,
            $ids,
            $usuario,
            Carbon::now()->format('Y-m-d H:i:s')
        ],
        self::EVO_BASIC_TEMPLATE
        );
    }
    public static function renderGetEvoAudit(string $nombre,string $client,string $to,string $from){
        return str_replace(self::VARS_GET_EVO,[
            $nombre,
            $client,
            $to,
            $from,
            Carbon::now()->format('Y-m-d H:i:s')
        ],
        self::GET_EVO_TEMPLATE
        );
    }
    public static function renderPrintEvoAudit(string $nombre, string $ids, string $client,string $from,string $to, string $ip)
    {
        return str_replace(
            self::VARS_PRINT_EVO,
            [
                $nombre, 
                $ids, 
                $client, 
                $from,
                $to,
                $ip, 
                Carbon::now()->format('Y-m-d H:i:s')
            ],
            self::PRINT_EVO_TEMPLATE
        );
    }

    public static function renderSerAutorizAppoTemplate(string $profesional, string $id, string $client, string $old, string $new) {
        $values = [$profesional, $id,  $old, $new,$client,Carbon::now()->format('Y-m-d H:i:s') ];

        return str_replace(
            self::VARS_SET_AUTORIZ_APPO, 
            $values,                     
            self::SET_AUTORIZ_APPO_TEMPLATE 
        );
    }
    

    
}
