<?php

namespace App\Repositories;

use App\Interfaces\ProcediproRepositoryInterface;
use App\Models\ProcediproModel;

class ProcediproRepository extends BaseRepository implements ProcediproRepositoryInterface{
    public function getProcediproByCod(int $id): ProcediproModel
    {   
        $query="select id,nombre,tipo_cita,tipo_registro,formatoimpevol from procedipro where id =?";
        $procedipros=self::sendQuery(query:$query,bindings:[$id]);
        return new ProcediproModel($procedipros[0]);
    }
}