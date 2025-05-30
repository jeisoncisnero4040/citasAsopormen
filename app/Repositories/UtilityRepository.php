<?php

namespace App\Repositories;

use App\Interfaces\UtilsPqrRepositoryInterface;
use App\Repositories\BaseRepository;

class UtilityRepository extends BaseRepository implements UtilsPqrRepositoryInterface{
    public function getAll():mixed{
        $query="
            select 
                id,
                nombre,
                'canal' as tipo
            FROM canales_pqr
            union 
            select 
                id,
                nombre,
                'tipo' as tipo
            FROM tipos_pqr
            union 
            select 
                id,
                nombre,
                'caracteristica' as tipo
            FROM caracteristicas_sogcs
            union 
            select 
                id,
                LTRIM(RTRIM(nombre)),
                'sede' as tipo
            FROM sede
            union 
            select 
                id,
                nombre,
                'usuario' as tipo
            FROM usuarios_pqrs
            union 
            select 
                id,
                nombre,
                'area' as tipo
            FROM sedes_areas_pqrs

            order by nombre asc
        ";
        return self::senqQuery($query,[]);
    }
}