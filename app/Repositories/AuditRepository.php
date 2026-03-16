<?php

namespace App\Repositories;

use App\Interfaces\AuditInterface;

class AuditRepository extends BaseRepository implements AuditInterface{
    public function saveAudit(string $audit, string $modulo,string $cedula): void
    {
        $query="INSERT INTO auditoria_mc (modulo,descripcion,cedula_usuario,fecha_creacion)
                VALUES(?,?,?,GETDATE())";
        self::sendQuery(query:$query,bindings:[$modulo,$audit,$cedula],typeConsult:'insert');
    }
}