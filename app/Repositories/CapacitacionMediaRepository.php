<?php

namespace App\Repositories;

use App\Interfaces\CapacitacionMediaPort;
use App\Commands\CapacitacionMediaCommand;
use App\Serializers\CapacitacionMediaSerializer;
use Illuminate\Support\Facades\DB;
use App\Dtos\GetCapacitacionMediaDto;
use App\Repositories\FilterBuilder;
use App\Repositories\QueryBuilder;

class CapacitacionMediaRepository extends BaseRepository implements CapacitacionMediaPort
{
    private string $table = 'video_capacitacion'; 

    public function create(CapacitacionMediaCommand $item): int
    {
        $data = CapacitacionMediaSerializer::toPersistenceArray($item);

        //$id = DB::table($this->table)->insertGetId($data);

        return 0;
    }
    public function get(GetCapacitacionMediaDto $dto): array
    {
        $query= "SELECT id, url,titulo, descripcion FROM {$this->table} WHERE 1=1 {{}}";

        $queryBuilder =QueryBuilder::create($query)
           ->withFilterBuilder($this->buildFilters($dto));
        
        $results = $this->sendQuery(query: $queryBuilder->toQuery(), bindings: $queryBuilder->getBindings());
        return array_map(function($item){
            return CapacitacionMediaSerializer::fromArrayToViewModel((array)$item);
        }, $results);
    }
    private function buildFilters(GetCapacitacionMediaDto $dto): FilterBuilder
    {
        $filters = FilterBuilder::create()
            ->add('name_modulo = ?', $dto->getModule()->value);  
        return $filters;
    }
}