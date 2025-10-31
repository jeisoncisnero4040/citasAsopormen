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
    public function getInfoClient(string $codigo):array{
        return self::sendQuery(query:"SELECT 
                            RTRIM(cli.codigo) AS codigo,
                            RTRIM(cli.nombre) AS nombre,
                            RTRIM(cli.nit_cli) AS cedula,
                            cli.f_nacio AS fecha_nacimiento,
                            RTRIM(cli.direcc) AS direccion,
                            RTRIM(cli.barrio) AS barrio,
                            RTRIM(cli.cel) AS celular,
                            RTRIM(mun.nombre) AS municipio,
                            cli.fechareg AS fecha_ingreso,
                            RTRIM(cli.regim) AS regimen,
                            RTRIM(cli.ecivil) AS estado_civil,
                            RTRIM(cli.tip_usuario) AS tipo_usuario,
                            RTRIM(cli.nivel) AS nivel,
                            RTRIM(cli.ocupacion) AS ocupacion,
                            DATEDIFF(YEAR,cli.f_nacio,GETDATE()) AS edad,
                            cli.creado,
                            cli.modificado AS ult_fecha_modi,
                            RTRIM(ent.clase) AS entidad,
                            RTRIM(cli.sexo) AS sexo,
                            RTRIM(cli.usumodi) AS usumodi,
                            RTRIM(cli.usucrea) AS usucrea,
                            RTRIM(cli.telacompañante) AS celular_responsable,
                            RTRIM(cli.nombreresponsable) AS responsable,
                            RTRIM(cli.parentresponsable) AS parentezco_responsable,
                            RTRIM(cli.rh) AS rh,
                            RTRIM(cli.acompañante) AS acompaniante,
                            RTRIM(cli.tel_acompa) AS celular_acompaniante,
                            RTRIM(cli.parentacompañante) AS parentezco_acompaniante,
                            CASE 
                                WHEN cli.activo = 0 THEN 'INACTIVO' 
                                ELSE 'ACTIVO' 
                            END AS estado
                        FROM 
                            cliente cli
                        INNER JOIN 
                            entidades ent ON ent.codigo = cli.codent2
                        INNER JOIN 
                            municipio mun ON mun.codigo = cli.cod_ciudad
                        WHERE 
                            cli.codigo = ?;
                        ",bindings:[$codigo]);
    }
}   