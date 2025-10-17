<?php

namespace App\Repositories;

use App\Constants\TemplatesQuerys;
use App\Interfaces\AuthsInterface;
use Illuminate\Support\Facades\DB;

class AuthsRepository extends BaseRepository implements AuthsInterface{
    public function getProfesionalsAuths(string $profesionalCed): array
    {
        $bindings=[$profesionalCed];
        $where="WHERE ci.cedprof= ?";
        $query=str_replace('{{}}',$where,TemplatesQuerys::TEMPLATE_QUERY_TO_GET_PROFESIONALS_AUTHS);
        return self::sendQuery(query:$query,bindings:$bindings);
    }

    public function getAuthsByIds(array $ids):array{
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where="WHERE au.id IN ($placeholders)";
        $query=str_replace('{{}}',$where,TemplatesQuerys::TEMPLATE_QUERY_TO_GET_PROFESIONALS_AUTHS);
        return self::sendQuery(query:$query,bindings:$ids);

    }

    public function closeAuth(string $razon, int $idAutoriz, string $profesional, array $ids): void
    {
        $query1 = "UPDATE autoriza SET cerrar_ord_asp = '1',
                        razon_cerrar_orden_asp = ?,
                        usu_cerrar_asp = ?,
                        fecha_cerrar_asp = GETDATE()
                        WHERE id = ?";
        $bindings1 = [$razon, $profesional, $idAutoriz];

        DB::transaction(function () use ($query1, $bindings1, $ids) {
            self::sendQuery(query: $query1, bindings: $bindings1, typeConsult: 'update');

            if (count($ids) > 0) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $queryDelete = "DELETE FROM citas WHERE id IN ($placeholders)";
                //self::sendQuery(query: $queryDelete, bindings: $ids, typeConsult: 'delete');
            }
        });
    }


    
}