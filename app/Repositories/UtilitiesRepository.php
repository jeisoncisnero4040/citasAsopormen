<?php

namespace App\Repositories;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\UtilitiesInterface;
use App\Models\DxModel;
use App\Models\UtilityModel;
use App\Repositories\BaseRepository;


class UtilitiesRepository extends BaseRepository implements UtilitiesInterface{
    function getEvoUtilities(): array
    {
        $query="SELECT descripcion, 
                CAST(codigo AS VARCHAR(10)) AS codigo,
                'via_ingreso' AS tipo
            FROM viaingreso

            UNION 
            SELECT finacons AS descripcion,
                CAST(codigo AS VARCHAR(10)) AS codigo,
                'finalidad' AS tipo
            FROM finacons

            UNION 
            SELECT causae AS descripcion,
                CAST(codigo AS VARCHAR(10)) AS codigo,
                'causa' AS tipo
            FROM causae
            WHERE codigo_SISPRO != ''

            UNION 
            SELECT detalle AS descripcion,
                CAST(codigo AS VARCHAR(10)) AS codigo,
                'lugar_atencion' AS tipo
            FROM lugar_atencion_asp

            UNION 
            SELECT parentezco AS descripcion,
                CAST(codigo AS VARCHAR(10)) AS codigo,
                'parentesco' AS tipo
            FROM parentezco

            UNION 
            SELECT descripcion,
                CAST(id AS VARCHAR(10)) AS codigo,
                'tipo_dx' AS tipo
            FROM tipos_dx
            ORDER BY tipo,codigo
            ";      
        $evoUtilities=BaseRepository::sendQuery(query:$query);

        return collect($evoUtilities)->mapInto(UtilityModel::class)->toArray();
    }
    public function searchDx(string $param): DxModel
    {
        $bindings=[$param,"%{$param}%"];
        $query="SELECT  top 1
                codigo,
                RTRIM(enfermedad) as enfermedad
            FROM enferm
            WHERE codigo = ? or enfermedad like ?";
        $dx=self::sendQuery(query:$query,bindings:$bindings);
        if(empty($dx)){
            throw new NotFoundException("No se han encontrado Diagnosticos coincidentes",404);
        }
        return new DxModel($dx[0]);
    }
}