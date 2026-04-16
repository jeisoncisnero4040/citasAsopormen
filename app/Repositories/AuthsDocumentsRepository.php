<?php

namespace App\Repositories;

use App\Interfaces\AuthsDocumentsPort;
use App\Commands\AuthsDocumentsCommand;
use App\Dtos\GetAuthsDocumentsDto;
use App\Serializers\AuthsDocumentsSerializer;
use Doctrine\DBAL\Query;
use OpenApi\Annotations\Get;

class AuthsDocumentsRepository extends BaseRepository implements AuthsDocumentsPort
{
    private string $table = 'documentos_autorizacion'; 
    private string $baseSelectQuery = "SELECT docs.id,
                                        tip_doc.nombre as nombre_tipo_documento,
                                        docs.tipo_documento_id as id_doc,
                                        docs.autorizacion_id as n_autoriza,
                                        docs.cedula_usuario,
                                        docs.codigo_eps,
                                        docs.fecha_registro,
                                        docs.documento_path,
                                        docs.historia
                                        FROM documentos_autorizacion docs
                                        INNER JOIN tipo_documento_autorizacion tip_doc ON tip_doc.id = docs.tipo_documento_id
                                        WHERE 1=1 {{}}";

    public function create(AuthsDocumentsCommand $item): int
    {
        $data = AuthsDocumentsSerializer::toPersistenceArray($item);
        return $this->transactionalQuery(function () use ($data) {
            return $this->insert($this->table, $data);
        });
    }
    public function get(GetAuthsDocumentsDto $dto): array
    {
        $query= $this->buildQuery($dto)->toQuery();
        $bindings = $this->buildQuery($dto)->getBindings();
        $rows= $this->sendQuery(
            query: $query,
            bindings: $bindings,
            typeConsult: 'select'
        );
        return array_map(function($row) {
            return AuthsDocumentsSerializer::fromArrayToViewModel((array) $row);
        }, $rows);

    }   
    public function getUtility(): array
    {
        $query = "SELECT CAST(id as VARCHAR) as cod, nombre FROM tipo_documento_autorizacion";
        return $this->sendQuery(query: $query);

    }
    public function getCommand(GetAuthsDocumentsDto $dto): array
    {

        $builder = $this->getQueryCommand($dto);
        $query = $builder->toQuery();
        $bindings = $builder->getBindings();
        $results = $this->sendQuery(
            query: $query,
            bindings: $bindings,
            typeConsult: 'select'
        );

        return array_map(function($row) {
            return AuthsDocumentsSerializer::fromArray((array) $row);
        }, $results);
    }

    public function delete(int $id): void{
        $query = "DELETE FROM documentos_autorizacion WHERE id = ?";
        $this->sendQuery(
            query: $query,
            bindings: [$id],
            typeConsult: 'delete'
        );
    }
    private function insert(string $table, array $data): int
    {
        $query = $this->buildCreateQuery($table, $data);
        return $this->sendQuery(
            query: $query,
            bindings: $this->makeValues($data),
            typeConsult: 'insert'
        );
    }
    private function buildQuery(GetAuthsDocumentsDto $dto): QueryBuilder
    {
        $builder = FilterBuilder::create();
        if($dto->isById()) {
            $builder->add('docs.id ', $dto->getId());
        }
        if($dto->isByNAutorizaAndClientCode()) {
            $builder->add('docs.autorizacion_id ', $dto->getNAutoriza());
            $builder->add('docs.historia', $dto->getClientCode());
        }
        return QueryBuilder::create()
            ->withBaseQuery($this->baseSelectQuery)
            ->withFilterBuilder($builder);
    }
    private function getQueryCommand(GetAuthsDocumentsDto $dto): QueryBuilder
    {
        $builder = FilterBuilder::create();
        if($dto->isById()) {
            $builder->add('id ', $dto->getId());
        }
        if($dto->isByNAutorizaAndClientCode()) {
            $builder->add('autorizacion_id ', $dto->getNAutoriza());
            $builder->add('historia', $dto->getClientCode());
        }
        return QueryBuilder::create()
            ->withBaseQuery($this->buildSelectBaseQueryCommand(
                AuthsDocumentsSerializer::fillableColumns(),
                $this->table
            ))
            ->withFilterBuilder($builder);
    }
}