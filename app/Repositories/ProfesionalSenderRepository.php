<?php

namespace App\Repositories;

use App\Dtos\GetProfesionalSerderDto;
use App\Interfaces\ProfesionalSenderPort;
use App\Models\ProfesionalSender;   
use App\Commands\ProfesionalSenderCommand;
use App\Serializers\ProfesionalSenderSerializer;

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
            $builder->add("codigo = ? ", $dto->getCode());
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
    public function create(ProfesionalSenderCommand $command): int
    {
        $data = ProfesionalSenderSerializer::serialize($command);
        $query = $this->buildCreateQuery('prof_Remitentes', $data);
        return $this->sendQuery($query,array_values($data),'insert');
    }
    public function utility(): array
    {
        return $this->sendQuery("SELECT 
								RTRIM(codigo) AS cod,
								nombre,
								'municipio' AS tipo,
								NULL AS referencia,
								NULL AS cod_referencia
							FROM municipio
                            UNION ALL

                                SELECT cod,
                                documento AS nombre,
                                'documento' as tipo,
                                NULL referencia,
                                NULL AS cod_referencia
                                FROM tipo_doc
                            ORDER BY tipo,nombre
                            ");
    }
}