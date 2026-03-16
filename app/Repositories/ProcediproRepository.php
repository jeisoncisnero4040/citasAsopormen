<?php

namespace App\Repositories;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\ProcedureModel;

class ProcediproRepository extends BaseRepository{
    public function getProcedureByName(string $name){
        $procedures= self::sendQuery(query:"SELECT nombre,sumable FROM procedipro WHERE nombre = ? ",bindings:[$name]);
        if(empty($procedures)){
            throw new NotFoundException("No se Encontraron procedimientos con el nombre $name",404);
        }
        return new ProcedureModel($procedures[0]);
    }
}