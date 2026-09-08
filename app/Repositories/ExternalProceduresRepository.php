<?php

namespace App\Repositories;
use App\Interfaces\ExternalProcedurePort;
use App\Models\ExternProcedure;
use App\Dtos\GetExrenalProcedureDto;
use App\Repositories\QueryBuilder;
use App\Repositories\FilterBuilder;


final class ExternalProceduresRepository extends BaseRepository implements ExternalProcedurePort
{
    public function getProceduresByEps(string $epsCode, string $covenantCode): array
    {
        $response = self::sendQuery("WITH tarifas AS (
                select en.tarifa from entidades en
                WHERE en.admini = ?
                AND en.codigo= ?
            )
            select RTRIM(pr.codigo) AS cod,
            RTRIM(pr.descrip) as nombre ,
            pr.inactivo,
            es.Descripcion AS especialidad
            from procdent pr
            inner join tarifas ta ON ta.tarifa = pr.cod_enti
            INNER JOIN especialidadAsp es ON es.id = pr.especialidadAsp
            WHERE pr.inactivo <> 1",
            [$epsCode, $covenantCode]
        );

        return collect($response)
            ->map(fn($item) => ExternProcedure::fromArray((array) $item))
            ->toArray();
    }
    public function getProcedures(GetExrenalProcedureDto $dto): array
    {
        $baseQuery="SELECT RTRIM(pr.codigo) AS cod,
            RTRIM(pr.descrip) as nombre ,
            es.Descripcion AS especialidad
            from procdent pr
            INNER JOIN especialidadAsp es ON es.id = pr.especialidadAsp
            WHERE 1=1
           {{}}";

        $filters = FilterBuilder::create();

        if($dto->getTarife() !== null){
            $filters->add("pr.cod_enti = ?", $dto->getTarife());
        }
        if($dto->getCode() !== null){
            $filters->add("pr.codigo = ?", $dto->getCode());
        }
        $filtersString = $filters->toFilter();
        $query=QueryBuilder::create()

            ->withBaseQuery($baseQuery)
            ->withFilters($filtersString->getQuery())
            ->toQuery();
        $response = self::sendQuery($query, $filtersString->getBindings());
        return collect($response)
            ->map(fn($item) => ExternProcedure::fromArray((array) $item))
            ->toArray();
    }
}