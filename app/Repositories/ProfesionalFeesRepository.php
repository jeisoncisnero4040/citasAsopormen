<?php

namespace App\Repositories;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\ProfesionalFeesInterface;
use App\Models\FeeModel;

class ProfesionalFeesRepository extends BaseRepository 
                                implements ProfesionalFeesInterface{

    public function getFeesProfesional(string $cedula,string $procedipro,string $entidad):FeeModel
    {
        $fees=self::sendQuery(query:"SELECT TOP 1  valor 
                                FROM tarifasHonorariosAsp 
                                WHERE documento = ?
                                AND procedipro = ?
                                AND entidad = ? ",
                                bindings:[$cedula,$procedipro,$entidad]);

        if(empty($fees)){
            throw new BadRequestException("El profesional ingresado no dispone de una tarifa para le entidad asociada o eps asociada o procedimiento asociado a la cita",404);
        }
        return new FeeModel($fees[0]);
    }

}