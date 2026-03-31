<?php

namespace App\Repositories;

use App\Dtos\GetProfesionalSerderDto;
use App\Interfaces\ProfesionalSenderPort;
use App\Models\ProfesionalSender;   

class ProfesionalSenderRepository extends BaseRepository implements ProfesionalSenderPort
{
    /**
     * @return array<ProfesionalSender>
     */
    public function getProfesionalSenders(GetProfesionalSerderDto $dto): array
    {
        $baseQuery = "SELECT codigo AS cod,
                        RTRIM(nombre) AS nombre
                        FROM prof_Remitentes
                    WHERE 1 = 1
                    {{}}";

        $builder = FilterBuilder::create();
        if ($dto->getCode() !== null) {
            $builder->add("codigo like ", "%" . $dto->getCode() . "%");
        }
        if ($dto->getName() !== null) {
            $params=explode(" ", $dto->getName());
            foreach ($params as $index => $param) {
                $builder->add("nombre COLLATE Latin1_General_CI_AI LIKE ? ", "%" . $param . "%");
            }
        }
        $query =QueryBuilder::create()
            ->withBaseQuery($baseQuery)
            ->withFilters($builder->toFilter()
            ->getQuery())
            ->toQuery();

            
        $result = self::sendQuery($query, 
                                $builder->toFilter()
                                ->getBindings());
        return collect($result)
            ->map(fn($item) => ProfesionalSender::fromArray((array)$item))
            ->toArray();
    }
}