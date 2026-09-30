<?php
namespace App\Repositories;

use App\Interfaces\EpsPort;
use App\Dtos\GetEpsDto;
use App\Domain\Eps;
use App\Repositories\FilterBuilder;
use App\Repositories\QueryBuilder;
use App\Serializers\EpsSerializer;

class EpsRepository extends  BaseRepository implements EpsPort
{
    

    public function get(GetEpsDto $dto): array
    {
        $query = QueryBuilder::create()
                ->withSelect('cliente',['RTRIM(nombre) AS nombre','codigo','RTRIM(nit_cli) AS nit_cli'])
                ->withFilterBuilder($this->buildFilters($dto));    
        $result = $this->execute($query);
        return array_map(fn($row) => EpsSerializer::fromPersistence($row), $result);
    }
    private function buildFilters(GetEpsDto $dto): FilterBuilder
    {
        $filters = FilterBuilder::create();
        if ($dto->getCode()) {
            $filters->add('codigo', $dto->getCode()->getCode());
        }
        if ($dto->getNit()) {
            $filters->add('nit_cli', $dto->getNit()->getCode());
        }
        return $filters;
    }
}