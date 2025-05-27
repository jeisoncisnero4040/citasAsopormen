<?php
namespace App\Repositories;
use App\Interfaces\ReasonsPqrRepositoryInterface;
use App\Repositories\BaseRepository;

class ReasonsPqrRepository extends BaseRepository implements ReasonsPqrRepositoryInterface{

    public function getAllReasons():mixed{
        $query="SELECT 
            id AS id_motivo,
            nombre,
            parent_id AS id_padre,
            nivel
        FROM motivos_pqr
        ORDER BY 
            CASE nivel 
                WHEN 'macromotivo' THEN 1
                WHEN 'general' THEN 2
                WHEN 'especifico' THEN 3
                WHEN 'tipo' THEN 4
                WHEN 'causa' THEN 5
                ELSE 6
            END, id;";
        return self::senqQuery(query:$query,bindings:[]);
    }
}