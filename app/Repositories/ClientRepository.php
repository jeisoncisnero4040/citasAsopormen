<?php

namespace App\Repositories;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\ClientModel;
use GuzzleHttp\Client;

use function Psy\bin;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface{
    public function searchClient(string $params): array
    {
        return self::sendQuery(
            query:" SELECT TOP 10
                RTRIM(cli.nombre) AS nombre, 
                cli.codigo AS code
                FROM cliente cli 
                WHERE  cli.nombre LIKE ?
                AND ok_ent = 0
                ORDER BY cli.nombre",
            bindings:["%$params%"]
        );
    }

    public function getClienByCode(string $codigo, string $profesional): ClientModel
    {
        $clients = self::sendQuery(
            query: "SELECT
                        RTRIM(cli.nombre) AS nombre, 
                        RTRIM(cli.nit_cli) AS cedula, 
                        DATEDIFF(YEAR, cli.f_nacio, GETDATE()) AS edad,
                        RTRIM(ent.clase) AS entidad,
                        cli.codigo AS cod,
                        ccb.ciudad
                    FROM cliente cli
                    INNER JOIN entidades ent ON ent.codigo = cli.codent2
                    LEFT JOIN CodigosCiudadesdebancos ccb ON ccb.codigociudad = cli.cod_ciudad 
                    WHERE cli.codigo = ?
                    AND EXISTS (
                            SELECT 1 
                            FROM citas 
                            WHERE cedprof = ? 
                            AND nro_hist = ?
                    )
                    ",
            bindings: [$codigo, $profesional, $codigo]
        );
        if(empty($clients)){
            throw new BadRequestException("Al parecer no tienes permisos suficientes para acceder a este usuario",400);
        }

        return new ClientModel($clients[0]);
    }

    public function getProceduresClient(string $codigo):array{
        $query= "SELECT DISTINCT procedipro as procedipro from citas
                    where nro_hist=? ";
        $bindings=[$codigo];
        return self::sendQuery(query:$query,bindings:$bindings);
    }
}   