<?php

namespace App\Serializers;
use App\Commands\AuthCommand;
use App\Domain\Consecutive;

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
            'nro'=>$auth->getConsecutive()->getConsecutive(),
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
            'es_provisional'=>$auth->isTempory() ? 1 : 0,


        ];
    }
    public static function fillable(): array
    {
        return [
            'id',
            'procedi',
            'cantidad',
            'fecha',
            'f_vence',
            'entidad',
            'nro',
            'historia',
            'n_autoriza',
            'dias',
            'usuario',
            'anulada',
            'f_registro',
            'usu_anul',
            'cdispo',
            'motivo',
            'observa',
            'autpaqd',
            'f_inicial',
            'paquete',
            'suspendida',
            'sede',
            'cerrar_ord_asp',
            'fecha_cerrar_asp',
            'usu_cerrar_asp',
            'razon_cerrar_orden_asp',
            'cambios_Asp',
            'tarifa',
            'remitente',
            'clinico_nuevo',
            'es_provisional'
        ];
    }
    public static function fromArray(array $data): AuthCommand
    {
        return new AuthCommand(
            authCode: $data['n_autoriza'] ?? null,
            cupCode: $data['procedi'] ?? null,
            amount: $data['cantidad'] ?? null,
            date: $data['fecha'] ?? null,
            expiredDate: $data['f_vence'] ?? null,
            epsCode: $data['entidad'] ?? null,
            clientCode: $data['historia'] ?? null,
            amountDays: $data['dias'] ?? null,
            userCreating: $data['usuario'] ?? null,
            anulated: (bool) ($data['anulada'] ?? 0),
            dateCreating: $data['f_registro'] ?? null,
            userAnulating: $data['usu_anul'] ?? null,
            assistedSessionsCounter: $data['cdispo'] ?? null,
            motiveAnulation: $data['motivo'] ?? null,
            observations: $data['observa'] ?? null,
            startDate: $data['f_inicial'] ?? null,
            covenantCode: $data['paquete'] ?? null,
            suspended: (bool) ($data['suspendida'] ?? 0),
            headQuarters: $data['sede'] ?? null,
            closed: (bool) ($data['cerrar_ord_asp'] ?? 0),
            dateClosed: $data['fecha_cerrar_asp'] ?? null,
            userClosed: $data['usu_cerrar_asp'] ?? null,
            motiveClosed: $data['razon_cerrar_orden_asp'] ?? null,
            changuesAsp: $data['cambios_Asp'] ?? null,
            tarifeCode: $data['tarifa'] ?? null,
            remitente: $data['remitente'] ?? null,
            newSystem: (bool) ($data['clinico_nuevo'] ?? 0),
            id: $data['id'] ?? null,
            consecutive: new Consecutive($data['nro'],'AU'),
            isTempory: (bool) ($data['es_provisional'] ?? 0)
        );
    }
    public static function getColumnsWithAlias(string $alias = 'a'): array
    {
        $columns = self::fillable();
        return array_map(fn($column) => "$alias.$column as $column", $columns);
    }

}