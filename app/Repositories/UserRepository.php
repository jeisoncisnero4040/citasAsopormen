<?php
namespace App\Repositories;

use App\Dtos\GetUsersDto;
use App\Repositories\BaseRepository;
use App\Repositories\FilterBuilder;
use App\Repositories\QueryBuilder;
use App\Interfaces\UserPort;


class UserRepository extends BaseRepository implements UserPort {
    static string $tableName = 'usuarios';
    static string $baseQuery = "SELECT RTRIM(cedula) AS cedula, RTRIM(responsable) AS nombre, rol_id FROM usuarios WHERE 1=1 {{}}";
    
    public  function get(GetUsersDto $dto): array {
        if($dto->getIsSearch()) {
            return $this->search($dto);
        }
        $filter = FilterBuilder::create();
        if($dto->getName()){
            $filter->add('nombre COLLATE Latin1_General_CI_AI LIKE ?', '%' . $dto->getName() . '%');
        }
        if($dto->getCedula()){
            $filter->add('cedula = ?', $dto->getCedula());
        }
        $query = QueryBuilder::create()
            ->withBaseQuery(self::$baseQuery)
            ->withFilterBuilder($filter);
        return self::sendQuery(query:$query->toQuery(), bindings:$query->getBindings());



    }
    private function search(GetUsersDto $dto): array {
        $filter = FilterBuilder::create();
        if($dto->isParamSeacrhOnlyNumeric()){
            $filter->add('cedula = ?', $dto->getParamSeacrh() );
        }
        if($dto->isParamSeacrhOnlyAlpha()){
            $filter->add('responsable COLLATE Latin1_General_CI_AI LIKE ?', '%' . $dto->getParamSeacrh() . '%');
        }
        if($dto->getOnlyAppointmentsPersonal()){
            $filter->addRaw('permisomc = 1');
        }

        $query = QueryBuilder::create()
            ->withBaseQuery(self::$baseQuery)
            ->withFilterBuilder($filter);
        return self::sendQuery(query:$query->toQuery(), bindings:$query->getBindings());
    }
}