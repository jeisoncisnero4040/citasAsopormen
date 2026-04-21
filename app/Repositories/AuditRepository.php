<?php

namespace App\Repositories;

use App\Interfaces\AuditInterface;
use App\Dtos\GetAuditDto;
use App\Repositories\BaseRepository;
use App\Repositories\FilterBuilder;
use App\Repositories\Filter;
use Doctrine\DBAL\Query;
use App\Models\Audit;

class AuditRepository extends BaseRepository implements AuditInterface{
    static string $tableName = 'auditoria_mc';
    static STRING  $BASE_QUERY = "SELECT a.id,u.responsable AS nombre, a.modulo, a.descripcion, a.cedula_usuario, a.fecha_creacion
                FROM auditoria_mc a
                LEFT JOIN usuarios u ON a.cedula_usuario = u.cedula
                WHERE 1=1 
                {{}}
                ORDER BY a.fecha_creacion DESC";
    public function saveAudit(string $audit, string $modulo,string $cedula): void
    {
        $query="INSERT INTO auditoria_mc (modulo,descripcion,cedula_usuario,fecha_creacion)
                VALUES(?,?,?,GETDATE())";
        self::sendQuery(query:$query,bindings:[$modulo,$audit,$cedula],typeConsult:'insert');
    }
    public function getAudits(GetAuditDto $dto):array
    {
        $filter = $this->buildFilters($dto);
        $query = QueryBuilder::create()
            ->withBaseQuery(self::$BASE_QUERY)
            ->withFilters($filter->getQuery())
            ->withBindings($filter->getBindings())
            ->toQuery();
        logger()->info("Consulta de auditoria: " . $query);
        logger()->info("Bindings de auditoria: " . json_encode($filter->getBindings()));
        $results = self::sendQuery(query:$query, bindings:$filter->getBindings(), typeConsult:'select');
        return array_map(fn($item) => Audit::fromArray((array) $item), $results);
    }
    private function buildFilters(GetAuditDto $dto): Filter
    {
        $filter = FilterBuilder::create();

        if ($dto->hasIdAppoinment()) {
            $filter->add('a.descripcion LIKE ?', '%' . $dto->getIdAppoinment() . '%');
            return $filter->toFilter();
        }

        if ($dto->hasAuthCode()) {
            $filter->add('a.descripcion LIKE ?', '%' . $dto->getAuthCode() . '%');
            return $filter->toFilter();
        }
        if ($dto->hasUser()) {
            $filter->add('a.cedula_usuario = ?', $dto->getUser());
        }
        $subFilter = [];
        $bindings = [];

        if ($dto->hasClient()) {
            $subFilter[] = "a.descripcion LIKE ?";
            $bindings[] = '%' . $dto->getClient() . '%';
        }

        if ($dto->hasProfesional()) {
            $subFilter[] = "a.descripcion LIKE ?";
            $bindings[] = '%' . $dto->getProfesional() . '%';
        }

        if (!empty($subFilter)) {
            $filter->addComplexFilter(
                implode(' AND ', $subFilter),
                $bindings
            );
        }

        $filter->add('a.fecha_creacion >= ?', $dto->getFrom());
        $filter->add('a.fecha_creacion <= ?', $dto->getTo());
        $filter->addRaw("a.modulo = 'citas'");
            

        return $filter->toFilter();
    }


}