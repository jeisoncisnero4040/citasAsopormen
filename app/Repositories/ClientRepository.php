<?php

namespace App\Repositories;

use App\Dtos\UpdateUserDto;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\ClientRepositoryInterface;
use App\Commands\ClientCommand;
use App\Models\ClientView;
use Exception;
use Illuminate\Support\Facades\DB;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function create(ClientCommand $client): int
    {
        try {

            DB::beginTransaction();

            $oldClient = DB::SELECT(
                query: "SELECT 1 
                        FROM cliente c WITH (UPDLOCK, HOLDLOCK) 
                        WHERE c.nit_cli = ? 
                        AND activo = '1' 
                        AND tip_iden = ?",
                bindings: [
                    $client->getNumDoc(),
                    $client->getDocumentType()
                ]
            );
            if (!empty($oldClient)) {
                throw new \Exception("Ya existe un paciente activo con este numero de documento");
            }

            $dataSave = $client->toPersistenceArray();
            $placeholders = self::makePlaceholders($dataSave);
            $columns = self::makeColumns($dataSave);
            $values = self::makeValues($dataSave);

            $dataSave2= $client->toClient2Array();
            $placeholders2=self::makePlaceholders(data:$dataSave2);
            $columns2 = self::makeColumns(data:$dataSave2);
            $values2 = self::makeValues(data:$dataSave2);

            $dataSave3 = $client->toNitsArray();
            $placeholders3 = self::makePlaceholders(data:$dataSave3);
            $columns3 = self::makeColumns(data:$dataSave3);
            $values3 = self::makeValues(data:$dataSave3);

            $query = "INSERT INTO cliente ($columns) VALUES ($placeholders)";
            $query2 = "INSERT INTO cliente2 ($columns2) VALUES ($placeholders2)";
            $query3 = "INSERT INTO nits ($columns3) VALUES ($placeholders3)";

            $id = self::sendQuery(
                query: $query,
                bindings: $values,
                typeConsult: 'insert'
            );
            self::sendQuery(
                query: $query2,
                bindings: $values2,
                typeConsult: 'insert'
            );
            self::sendQuery(
                query: $query3,
                bindings: $values3,
                typeConsult: 'insert'
            );

            DB::commit();
            return $id;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServerErrorException(
                "No se pudo guardar el nuevo cliente: " . $e->getMessage(),
                500
            );
        }
    }
    /**
     * @return array<ClientView>
     */
    public function get(array $filters = []): array
    {
        $query = "SELECT 
            RTRIM(c.codigo) AS codigo,
            c.zona,
            RTRIM(c.nombre) AS nombre,
            RTRIM(c.nit_cli) AS nit_cli,
            RTRIM(c.direcc) AS direcc,
            RTRIM(c.barrio) AS barrio,

            c.municipio AS municipio_codigo,
            c.pais,

            c.fechareg,
            c.f_nacio,
            c.activo,
            c.ecivil,
            c.tip_usuario,
            c.contrib,
            c.ocupacion,
            c.creado,
            RTRIM(c.codent) AS cod_entidad,
            RTRIM(c.codent2) AS cod_convenio,

            RTRIM(c.sexo) AS sexo,

            c.pn,
            c.sn,
            c.pa,
            c.sa,
            c.foto,

            c.email,
            c.tip_iden,
            c.usucrea,
            c.usumodi,
            c.sabefirmar,

            c.telacompañante AS telacompanante,
            c.parentresponsable,
            c.cedulaacompañante,

            c.G_poblacional,
            c.P_etnica,

            c.rh,

            RTRIM(c.cel) AS cel,
            c.tel_acompa,

            c.lugarnac,
            c.niv_academi,
            c.n_hijos,

            c2.tip_discapacidad,
            c2.grupo_sisben,
            c2.observaciones_asp,
            c2.pn_responsable,
            c2.sn_responsable,
            c2.pa_responsable,
            c2.sa_responsable,


            RTRIM(ccb.ciudad) AS municipio,
            p.NOMBRE AS pais_text,

            r.tipodiag AS regimen,

            REPLACE(REPLACE(ocupa.descrip, CHAR(13), ''), CHAR(10), '') AS ocupacion_text,

            eps.nombre AS entidad,

            ent.clase AS convenio,

            s.nombre AS sexo_text,

            doc.documento,

            et.etnia,

            gp.grupo,

            dis.discapacidad,
            esco.escolaridad

        FROM cliente c
        INNER JOIN cliente2 c2 ON c2.codigo = c.codigo
        LEFT JOIN CodigosCiudadesdebancos ccb ON ccb.codigociudad = c.cod_ciudad
        LEFT JOIN paises p ON p.CODIGO = c.pais
        LEFT JOIN tipodiag r ON r.codigo = c.contrib
        LEFT JOIN CIUO ocupa ON ocupa.cod = c.ocupacion
        LEFT JOIN cliente eps ON eps.codigo = c.codent
        INNER JOIN entidades ent ON ent.codigo = c.codent2
        LEFT JOIN sexoAsp s ON s.sigla = c.sexo
        LEFT JOIN tipo_doc doc ON doc.tipo = c.tip_iden
        LEFT JOIN etnias_asp et ON et.id = CAST(c.P_etnica AS int)
        LEFT JOIN grupo_poblacional gp ON gp.id = CAST(c.G_poblacional AS int)
        LEFT JOIN tipo_discapacidad dis ON dis.id = CAST(c2.tip_discapacidad AS int)
        LEFT JOIN escolaridad esco ON esco.id = CAST(c.niv_academi AS int)

        WHERE 1=1
        ";

        $bindings = [];
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                $query .= " AND c.$column = ?";
                $bindings[] = $value;
            }
        }
        $clients = self::sendQuery(
            query: $query,
            bindings: $bindings
        );
        return collect($clients)
            ->map(fn($client) => ClientView::fromArray((array)$client))
            ->toArray();
    }
    public function updateClient(string $codigo, UpdateUserDto $dto): int
    {
        $data = $dto->toPersistenceArray();

        $setClause = self::makeSetClause(data: $data);
        $values = self::makeValues(data: $data);

        $query = "UPDATE cliente SET {$setClause} WHERE codigo = ?";

        return self::sendQuery(
            query: $query,
            bindings: [...$values, $codigo],
            typeConsult: 'update'
        );
    }
    public function getPwdByCodigo(string $codigo): array
    {
        return self::sendQuery(query:"SELECT codigo,user_password_mc AS password FROM cliente2 WHERE codigo = ? ",bindings:[$codigo]);
    }
    public function setPws(string $codigo, string $newPassword): int
    {
        return self::sendQuery(query:"UPDATE cliente2 SET user_password_mc = ? WHERE codigo = ?",bindings:[$newPassword,$codigo],typeConsult:'update') ;       

    }
    public function search(array $filters): array
    {
        $query = "SELECT TOP 20 
                    RTRIM(codigo) AS codigo,
                    RTRIM(nombre) AS nombre
                FROM cliente
                WHERE ok_ent = 0";

        $conditions = [];
        $bindings = [];
        foreach ($filters as $column => $value) {
            if ($column === 'nombre' && is_array($value)) {
                foreach ($value as $word) {
                    $conditions[] = "$column COLLATE Latin1_General_CI_AI LIKE ?";
                    $bindings[] = "%$word%";
                }
            } 
            else {
                $conditions[] = "$column = ?";
                $bindings[] = $value;
            }
        }

        if (!empty($conditions)) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }

        $query .= " AND activo = 1 ORDER BY nombre";
        

        return self::sendQuery(query: $query, bindings: $bindings);
    }
    public function update(ClientCommand $client): int
    {
        $codigo = $client->getCode();
        $nit=$client->getNumDoc();

        $dataSave = $client->toPersistenceArray();
        $clause = self::makeSetClause($dataSave);
        $values = self::makeValues($dataSave);

        $dataSave2= $client->toClient2Array();
        $clause2 = self::makeSetClause(data:$dataSave2);
        $values2 = self::makeValues(data:$dataSave2);

        $dataSave3 = $client->toNitsArray();
        $clause3 = self::makeSetClause(data:$dataSave3);
        $values3 = self::makeValues(data:$dataSave3);


        $query1 = "UPDATE cliente SET $clause WHERE codigo = ?";
        $query2 = "UPDATE cliente2 SET $clause2 WHERE codigo = ?";
        $query3 = "UPDATE nits SET $clause3 WHERE nit = ?";

        try{

            DB::beginTransaction();
            $id = self::sendQuery(
                query: $query1,
                bindings: [...$values,$codigo],
                typeConsult: 'update'
            );
            self::sendQuery(
                query: $query2,
                bindings: [...$values2,$codigo],
                typeConsult: 'update'
            );
            self::sendQuery(
                query: $query3,
                bindings: [...$values3,$nit],
                typeConsult: 'update'
            );
            DB::commit();
            return $id;

        } catch (\Exception $e) {

            DB::rollBack();

            throw new ServerErrorException(
                "No se pudo Actualizar el Nuevo el nuevo cliente: " . $e->getMessage(),
                500
            );
        }

    }
    public function getLastHistory(): array
    {
        return DB::SELECT(
                query: "SELECT TOP 1 codigo 
                        FROM cliente c WITH (UPDLOCK, HOLDLOCK)
                        ORDER BY id DESC"
            );
    }
    public function toggleActive(string $code, bool $active): array
    {
        $queryToggle = "UPDATE cliente SET activo = ? WHERE codigo = ?";

        $queryDeleteAppos = "SELECT id 
                            FROM citas 
                            WHERE nro_hist = ?
                            AND fecha > CAST(GETDATE() AS DATE)
                            AND cancelada != '1'
                            AND asistio != '1'
                            AND na != '1'";

        try {

            DB::beginTransaction();
            DB::update(
                query: $queryToggle,
                bindings: [$active ? '1' : '0', $code]
            );
            $idsToDelete = DB::select($queryDeleteAppos, [$code]);
            $idsList = collect($idsToDelete)
                ->pluck('id')
                ->toArray();
            if (!empty($idsList)) {
                $placeholders = self::makePlaceholdersPlains(items: $idsList);
                DB::delete(
                    query: "DELETE FROM citas WHERE id IN ($placeholders)",
                    bindings: $idsList
                );
            }
            DB::commit();
            return $idsList;

        } catch (\Exception $e) {

            DB::rollBack();

            throw new ServerErrorException(
                "No se pudo cambiar el estado del cliente: " . $e->getMessage(),
                500
            );
        }
    }
        
    
}
