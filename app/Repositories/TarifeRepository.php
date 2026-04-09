<?php

namespace App\Repositories;
use App\Interfaces\TarifePort;
use App\Models\Tarife;


class TarifeRepository extends BaseRepository implements TarifePort{

    public function getTarife(string $code): ?array
    {
        return [];
    }
    public function getTarifeByEpsAndCovenant(string $epsCode, string $covenantCode): array
    {
        $baseQuery = "SELECT codigo AS eps,id,tarifa,admini convenio FROM entidades WHERE admini = ? AND codigo = ?";
        $response = self::sendQuery($baseQuery, [$epsCode, $covenantCode]);
        return collect($response)->map(fn($item) => Tarife::fromArray((array) $item))->toArray();
    }
}