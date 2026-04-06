<?php
namespace App\Models;

class InformesModel extends BaseModel{

    public function getUserWhithNewProcedure(string $from,string $to,$procedure){
        $query="WITH primeras_citas AS (
                    SELECT 
                        nro_hist,
                        procedim,
                        fecha,
                        sede,
                        autoriz,
                        tiempo,
                        registro,
                        procedipro,
                        ROW_NUMBER() OVER (
                            PARTITION BY nro_hist, procedipro
                            ORDER BY fecha ASC
                        ) AS rn
                    FROM citas WITH (INDEX(IX_citas_hist_proc_fecha))
                    WHERE fecha BETWEEN CONVERT(smalldatetime, ?, 120)
                                AND DATEADD(DAY, 90, CONVERT(smalldatetime, ?, 120))
                ),

                procedimientos AS (
                    SELECT DISTINCT nro_hist, procedipro
                    FROM citas WITH (INDEX(IX_citas_hist_proc_fecha))
                    WHERE fecha < CONVERT(smalldatetime, ?, 120)
                )

                SELECT 
                    c.codigo,
                    c.nit_cli,
                    c.tip_iden,
                    c.nombre,
                    c.cel AS celular,
                    s.nombre AS sede,
                    e.nombre AS entidad, 
                    td.tipodiag AS tipo,
                    pc.procedipro AS procedimiento,
                    pc.fecha AS primer_cita,
                    pc.registro AS nombre_quien_registra,
                    au.fecha AS fecha_ingreso_orden,
                    au.usuario AS ingreso_autorizacion,
                    au.cantidad,
                    au.f_inicial AS fecha_inicial,
                    au.f_vence AS fecha_cierre,
                    au.n_autoriza
                FROM primeras_citas pc
                JOIN cliente c ON c.codigo = pc.nro_hist
                LEFT JOIN tipodiag td ON td.codigo = c.contrib
                LEFT JOIN cliente e ON e.codigo = c.codent AND e.socie <> ''
                JOIN sede s ON s.cod = pc.sede
                JOIN autoriza au 
                    ON pc.autoriz = au.n_autoriza 
                AND pc.tiempo = au.procedi
                WHERE au.fecha BETWEEN CONVERT(smalldatetime, ?, 120)
                                AND CONVERT(smalldatetime, ?, 120)
                AND pc.procedipro = ?
                AND NOT EXISTS (
                    SELECT 1 
                    FROM procedimientos pr
                    WHERE pr.procedipro = ? 
                    AND pr.nro_hist = c.codigo
                )
                AND pc.rn = 1
                ORDER BY c.nombre;";
        $bindings=[$from,$from,$from,$from,$to,$procedure,$procedure];
        return self::senqQuery($query,$bindings);
    }

    public function getNewClientsByProcedure(string $from,string $to){
        $query=" WITH primeras_citas AS (
                SELECT 
                    c.nro_hist,
                    ca.procedim,
                    ca.fecha,
                    ca.autoriz,
                    ca.tiempo,
                    ca.registro,
                    ca.procedipro
                FROM (
                    SELECT DISTINCT nro_hist, procedipro
                    FROM citas
                    WHERE fecha BETWEEN CONVERT(smalldatetime,?,120) --from
                                    AND DATEADD(DAY, 90, CONVERT(smalldatetime,?,120))--from
                ) c
                CROSS APPLY (
                    SELECT TOP 1 c2.procedim, c2.fecha, c2.autoriz, c2.tiempo, c2.registro, c2.procedipro
                    FROM citas c2
                    WHERE c2.nro_hist = c.nro_hist
                    AND c2.procedipro = c.procedipro
                    AND c2.fecha BETWEEN CONVERT(smalldatetime,?,120) --from
                                    AND DATEADD(DAY, 90, CONVERT(smalldatetime,?,120))--from
                    ORDER BY c2.fecha ASC
                ) ca
            ),
            procedimientos AS (
                SELECT DISTINCT c.nro_hist, c.procedipro
                FROM citas c
                WHERE c.fecha < CONVERT(smalldatetime,?,120)--from
                AND EXISTS (
                    SELECT 1 
                    FROM primeras_citas pc
                    WHERE pc.nro_hist = c.nro_hist
                )
            )
            SELECT 
                pc.procedipro AS procedimiento,
                COUNT(*) AS usuarios
            FROM primeras_citas pc
            JOIN autoriza au 
                ON pc.autoriz = au.n_autoriza 
            AND pc.tiempo  = au.procedi
            WHERE au.fecha BETWEEN CONVERT(smalldatetime,?,120) --from
                            AND CONVERT(smalldatetime,?,120)--to
            AND NOT EXISTS (
                SELECT 1
                FROM procedimientos pr
                WHERE pr.nro_hist   = pc.nro_hist
                    AND pr.procedipro = pc.procedipro
            )
            GROUP BY pc.procedipro
            ORDER BY usuarios;

        
        ";
        $bindings=[$from,$from,$from,$from,$from,$from,$to];
        return self::senqQuery($query,$bindings);
    }
    public function getUsersWithOutAppoiments(string $from,string $to){
        $query="WITH entitabla AS (
                    SELECT * 
                    FROM cliente 
                    WHERE socie <> ''
                ), 
                tmp2 AS (
                    SELECT 
                        c.nit_cli, 
                        c.codigo, 
                        c.nombre, 
                        c.tip_iden, 
                        c.creado, 
						c.usucrea,
                        entitabla.nombre AS entidad,  
                        td.tipodiag AS tipo
                    FROM cliente c
                    INNER JOIN tipodiag td ON td.codigo = c.contrib 
                    INNER JOIN entitabla ON entitabla.codigo = c.codent 
                    WHERE c.creado BETWEEN CONVERT(smalldatetime, ?, 120) 
                                    AND CONVERT(smalldatetime, ?, 120)
                    AND NOT EXISTS (
                            SELECT 1 
                            FROM citas ci
                            WHERE ci.nro_hist = c.codigo
                            AND ci.fecha > CONVERT(smalldatetime, ?, 120) 
                        )
                ), 
                admitari AS (
                    SELECT codigo, tarifa 
                    FROM entidades
                ), 
                convetari AS (
                    SELECT 
                        ad.codigo AS admini, 
                        ad.tarifa, 
                        pro.codigo, 
                        pro.descrip  
                    FROM admitari ad 
                    INNER JOIN procdent pro ON ad.tarifa = pro.cod_enti
                )

                SELECT 
                    t2.nit_cli, 
                    t2.nombre, 
                    t2.tip_iden, 
                    t2.creado, 
                    t2.entidad, 
                    t2.tipo, 
					t2.usucrea AS quien_ingreso_cliente,
                    au.procedi,  
                    au.n_autoriza, 
					au.cantidad,
					au.f_inicial AS fecha_inicial,
					au.f_vence AS fecha_final,
                    (
                        SELECT descrip 
                        FROM convetari 
                        WHERE convetari.admini = au.paquete 
                        AND convetari.codigo = au.procedi
                    ) AS procedimiento,  
                    au.usuario AS ingreso 
                FROM tmp2 t2 
                INNER JOIN autoriza au ON t2.codigo = au.historia
                WHERE au.anulada = 0 
                AND au.suspendida = 0;
                ";
        $bindings=[$from,$to,$from];
        return self::senqQuery($query,$bindings);
    }
    public function countCitasByEntity(string $from,string $to){
        $bindings=[$from,$to,$from,$to];
        $query="SELECT  
            COUNT(*) AS citas,
            LTRIM(RTRIM(ent.clase)) AS entidad,
            ci.fecha
        FROM citas ci
        INNER JOIN entidades ent ON ci.codent2 = ent.codigo
        WHERE ci.fecha BETWEEN CONVERT(SMALLDATETIME,?, 120) AND CONVERT(SMALLDATETIME, ?, 120)
        GROUP BY ent.clase, ci.fecha

        UNION ALL

        SELECT  
            COUNT(*) AS citas,
            'Total' AS entidad,
            fecha
        FROM citas
        WHERE fecha BETWEEN CONVERT(SMALLDATETIME, ?, 120) AND CONVERT(SMALLDATETIME, ?, 120)
        GROUP BY fecha
        order by fecha,entidad";
        return self::senqQuery($query,$bindings);
    }
    public function getOldUserInProcedipro(string $from,string $to,$procedure){
        $query="WITH CitasRango AS (
                SELECT
                    nro_hist,
                    fecha,
                    sede,
                    autoriz,
                    tiempo,
                    registro,
                    ROW_NUMBER() OVER (
                        PARTITION BY nro_hist
                        ORDER BY fecha ASC
                    ) AS rn
                FROM citas WITH (INDEX(IX_citas_hist_proc_fecha))
                WHERE procedipro = ?
                AND fecha BETWEEN CONVERT(smalldatetime, ?, 120)
                                AND DATEADD(DAY, 90, CONVERT(smalldatetime, ?, 120))
            ),

            Historial AS (
                SELECT
                    nro_hist,
                    MAX(CASE 
                            WHEN fecha >= DATEADD(YEAR, -2, CONVERT(smalldatetime, ?, 120))
                            AND fecha <  CONVERT(smalldatetime, ?, 120)
                            THEN 1 
                        END) AS TieneReciente,
                    MAX(CASE 
                            WHEN fecha < DATEADD(YEAR, -2, CONVERT(smalldatetime, ?, 120))
                            THEN 1 
                        END) AS TieneAntiguo
                FROM citas WITH (INDEX(IX_citas_hist_proc_fecha))
                WHERE procedipro = ?
                AND fecha < CONVERT(smalldatetime, ?, 120)
                GROUP BY nro_hist
            )

            SELECT
                c.codigo,
                c.nit_cli,
                c.tip_iden,
                c.nombre,
                c.cel AS celular,
                s.nombre AS sede,
                e.nombre AS entidad,
                td.tipodiag AS tipo,
                cr.fecha AS primer_cita,
                cr.registro AS nombre_quien_registra,
                au.fecha AS fecha_ingreso_orden,
                au.usuario AS ingreso_autorizacion,
                au.cantidad,
                au.f_inicial AS fecha_inicial,
                au.f_vence AS fecha_cierre,
                au.n_autoriza
            FROM CitasRango cr
            JOIN Historial h 
                ON h.nro_hist = cr.nro_hist
            JOIN cliente c 
                ON c.codigo = cr.nro_hist
            LEFT JOIN tipodiag td 
                ON td.codigo = c.contrib
            LEFT JOIN cliente e 
                ON e.codigo = c.codent AND e.socie <> ''
            JOIN sede s 
                ON s.cod = cr.sede
            JOIN autoriza au 
                ON cr.autoriz = au.n_autoriza
            AND cr.tiempo  = au.procedi
            WHERE cr.rn = 1
            AND h.TieneReciente IS NULL
            AND h.TieneAntiguo = 1
            AND au.fecha BETWEEN CONVERT(smalldatetime, ?, 120)
                                AND CONVERT(smalldatetime, ?, 120)
            ORDER BY c.nombre;";

            $bindings = [
                $procedure,  // CitasRango procedipro
                $from,       // rango inicio
                $to,         // rango fin (+90 días desde aquí)
                
                $from,       // Historial corte -2 años
                $from,       // Historial limite superior recientes
                $from,       // Historial corte -2 años (antiguos)
                $procedure,  // Historial procedipro
                $from,       // Historial fecha < inicio
                
                $from,       // au.fecha inicio
                $to          // au.fecha fin
            ];

        return self::senqQuery($query,$bindings);}
    
        public function getOldUsersWithoutFutureAppoiments(string $from,string $to){    
        {
            $query = "WITH pacientes_con_citas AS (
                    SELECT DISTINCT nro_hist
                    FROM citas
                ),

                clientes as (SELECT 
                    c.nit_cli,
                    c.codigo,
                    c.nombre,
                    c.tip_iden,
                    c.creado,
                    c.usucrea,
                    e.nombre AS entidad,
                    td.tipodiag AS tipo
                FROM cliente c
                INNER JOIN pacientes_con_citas pcc 
                    ON pcc.nro_hist = c.codigo
                INNER JOIN tipodiag td 
                    ON td.codigo = c.contrib
                LEFT JOIN cliente e 
                    ON e.codigo = c.codent AND e.socie <> ''
                WHERE 
                    -- No tenga citas futuras
                    NOT EXISTS (
                        SELECT 1
                        FROM citas ci
                        WHERE ci.nro_hist = c.codigo
                        AND ci.fecha > CONVERT(smalldatetime, ?, 120)
                    )
                ),
				autorizaciones as (SELECT 

                    au.n_autoriza,
                    au.f_inicial AS fecha_inicial,
                    au.f_vence AS fecha_final,
                    au.usuario AS ingreso_autorizacion,
					au.historia,
					ROW_NUMBER () OVER(
						partition by au.historia
						order by id desc
					) as rn 
					from autoriza au 
					INNER JOIN  clientes cli ON au.historia = cli.codigo
											AND au.anulada = 0
											AND au.suspendida = 0
					WHERE au.f_inicial between CONVERT(smalldatetime,?,120) AND 
					CONVERT(smalldatetime,?,120)
				)


				select cli.*,au.* from clientes cli
				INNER JOIN autorizaciones au ON au.historia = cli.codigo
												AND au.rn = 1
            ";

            $bindings = [$from, $from, $to];

            return self::senqQuery($query, $bindings);
        }
        }
}
