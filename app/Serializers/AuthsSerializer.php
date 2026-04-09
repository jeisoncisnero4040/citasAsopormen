<?php

namespace App\Serializers;
use App\Commands\AuthCommand;

class AuthsSerializer
{
    public static function toPersistence(AuthCommand $auth): array
    {
        return [
            'procedi'=>$auth->getCupCode(),
            'cantidad'=>$auth->getAmount(),
            'sdt_fecha'=>$auth->getDate(),
            'sdt_f_vence'=>$auth->getExpiredDate(),
            'entidad'=>$auth->getEpsCode(),
            'nro'=>$auth->getConsecutive(),
            'historia'=>$auth->getClientCode(),
            'n_autoriza'=>$auth->getAuthCode(),
            'dias'=>$auth->getAmountDays(),
            'usuario'=>$auth->getUserCreating(),
            'anulada'=>$auth->isAnulated() ? 1 : 0,
            'sdt_f_registro'=>$auth->getDateCreating(),
            'usu_anul'=>$auth->getUserAnulating(),
            'cdispo'=>$auth->getAssistedSessionsCounter(),
            'motivo'=>$auth->getMotiveAnulation(),
            'observa'=>$auth->getObservations(),
            'autpaqd'=>0,
            'sdt_f_inicial'=>$auth->getStartDate(),
            'paquete'=>$auth->getCovenantCode(),
            'suspendida'=>$auth->isSuspended() ? 1 : 0,
            'sede'=>$auth->getHeadQuarters(),
            'cerrar_ord_asp'=>$auth->isClosed() ? 1 : 0,
            'sdt_fecha_cerrar_asp'=>$auth->getDateClosed(),
            'usu_cerrar_asp'=>$auth->getUserClosed(),
            'razon_cerrar_orden_asp'=>$auth->getMotiveClosed(),
            'cambios_Asp'=>$auth->getChanguesAsp(),
            'tarifa'=>$auth->getTarifeCode(),
            'remitente'=>$auth->getRemitente(),
            'clinico_nuevo'=>$auth->isNewSystem() ? 1 : 0,


        ];
    }

}