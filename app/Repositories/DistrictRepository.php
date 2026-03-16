<?php

namespace App\Repositories;

use App\Models\District;

class DistrictRepository extends BaseRepository{
    /**
     * @return array<District>
     */
    public function getByCode(string $code):array{
        $query = "SELECT 
                RTRIM(codigo) AS codigo,
                RTRIM(nombre) AS nombre,
                RTRIM(departa) AS departa,
                RTRIM(pais) AS pais 
                FROM  municipio 
                WHERE codigo = ?";
        $result = self::sendQuery(query:$query,bindings:[$code]);
        return collect($result)->map(fn($v) =>District::fromArray((array)$v) )->toArray();
    }
}