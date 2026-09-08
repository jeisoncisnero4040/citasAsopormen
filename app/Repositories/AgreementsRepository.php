<?php

namespace App\Repositories;

use App\Interfaces\AgreementsPort;
use App\Commands\AgreementsCommand;
use App\Serializers\AgreementsSerializer;
use Illuminate\Support\Facades\DB;
use App\Dtos\GetAgreementsDto;
use App\Models\AgreementsViewModel;


class AgreementsRepository extends BaseRepository implements AgreementsPort
{
    private string $table = 'entidades'; 
    private string $baseQuery = "SELECT codigo,
                                RTRIM(clase) AS nombre,
                                RTRIM(admini) AS eps_code,
                                tarifa,
                                es_pbs  
                                FROM entidades
                                WHERE 1 = 1 
                                {{}}";

    public function create(AgreementsCommand $item): int
    {
        $data = AgreementsSerializer::toPersistenceArray($item);

        //$id = DB::table($this->table)->insertGetId($data);

        return 0;
    }
    public function get(GetAgreementsDto $dto): array
    {
        $query = QueryBuilder::create($this->baseQuery)
                    ->withFilterBuilder($this->buildFilter($dto));
        $result = $this->sendQuery($query->toQuery(), $query->getBindings());
        return array_map(fn($item) => AgreementsSerializer::fromPersistence($item), $result);
    }
    public function buildFilter(GetAgreementsDto $dto): FilterBuilder{
        $filter = FilterBuilder::create();
        if($dto->getEpsCode()){
            $filter = $filter->add('admini', $dto->getEpsCode());
        }
        if($dto->getAgreementCode()){
            $filter = $filter->add('codigo', $dto->getAgreementCode());
        }
        if($dto->getTarife()){
            $filter = $filter->add('tarifa', $dto->getTarife());
        }
        return $filter;
    }
}