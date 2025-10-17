<?php

namespace App\Repositories;

use App\Interfaces\AuditInterface;

class AuditRepository extends BaseRepository implements AuditInterface{
    public function saveAudit(string $audit, string $modulo): void
    {
        $query="INSERT INTO auditoria_mc (modulo,descripcion,fecha_creacion)
                VALUES(?,?,GETDATE())";
        self::sendQuery(query:$query,bindings:[$modulo,$audit],typeConsult:'insert');
    }
}