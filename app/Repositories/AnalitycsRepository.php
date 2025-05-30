<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Interfaces\AnalitycsRepositoryInterface;


class AnalitycsRepository extends BaseRepository implements AnalitycsRepositoryInterface{
    

    public function pqrsHistory(string $from, string $to,string $fromTendencie,string $toTendencie): mixed
    {
        $bindings=[$from,$to];
        $bindingsTendencie=[$fromTendencie,$toTendencie];
        $query1="
            WITH pqrs_filtrado AS (
                SELECT
                    p.id,
                    p.tipo_id,
                    p.estado,
                    p.fecha_creacion,
                    p.sede_id,
                    p.area_id,
                    CASE
                        WHEN p.fecha_cierre IS NOT NULL THEN DATEDIFF(HOUR, p.fecha_creacion, p.fecha_cierre)
                        ELSE NULL
                    END AS tiempo_total,
                    CASE
                        WHEN p.fecha_envio_area IS NOT NULL AND p.fecha_respuesta IS NOT NULL THEN DATEDIFF(HOUR, p.fecha_envio_area, p.fecha_respuesta)
                        ELSE NULL
                    END AS tiempo_en_area,
                    p.url_respuesta
                FROM pqrs p 
                WHERE
                    p.fecha_creacion BETWEEN CONVERT(smalldatetime, '2025-04-01', 120) AND CONVERT(smalldatetime, '2025-04-30', 120)

            )

            SELECT 
                pf.id,
                pf.tiempo_total,
                pf.tiempo_en_area,
                pf.url_respuesta,
                pf.fecha_creacion,
                t.nombre AS tipo,
                t.dias_respuesta * 24 as tiempo_respuesta,
                s.nombre AS sede,
                a.nombre AS area,
                CASE
                    WHEN pf.tiempo_total IS NOT NULL  THEN pf.estado

                    ELSE 'indefinido'
                END AS estado,
                CASE
                    WHEN pf.tiempo_total IS NOT NULL AND (t.dias_respuesta * 24 - pf.tiempo_total) > 0 THEN '1'
                    ELSE '0'
                END AS tiempo_cumplido
            FROM pqrs_filtrado pf
            INNER JOIN tipos_pqr t ON pf.tipo_id = t.id
            INNER JOIN sede s ON pf.sede_id =s.id
            INNER JOIN sedes_areas_pqrs a on pf.area_id = a.id;


        ";

        $query2="SELECT
                    count(*) AS pqrs,
                    t.nombre
                FROM  pqrs p
                INNER JOIN  tipos_pqr t ON p.tipo_id=t.id 
                WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime,?, 120) AND CONVERT(smalldatetime, ?, 120)
                group by nombre 
                ";
        
        $query3="WITH pqrs_por_tipo AS (
                    SELECT 
                        FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes, 
                        t.nombre AS nombre,
                        COUNT(*) AS pqrs
                    FROM 
                        pqrs p
                    JOIN 
                        tipos_pqr t ON p.tipo_id = t.id
                    WHERE 
                        p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime, ?, 120)
                    GROUP BY 
                        FORMAT(p.fecha_creacion, 'yyyy-MM'),
                        t.nombre
                ),
                totales_por_mes AS (
                    SELECT 
                        mes,
                        'Total' AS nombre,
                        SUM(pqrs) AS pqrs
                    FROM 
                        pqrs_por_tipo
                    GROUP BY 
                        mes
                )

                SELECT mes, nombre, pqrs
                FROM (
                    SELECT * FROM pqrs_por_tipo
                    UNION ALL
                    SELECT * FROM totales_por_mes
                ) AS resultado
                ORDER BY 
                    mes ASC, 
                    CASE WHEN nombre = 'Total' THEN 'zzz' ELSE nombre END;
                ";
        $query4="
            select count(*) as pqrs,
                c.nombre
            from pqrs p
            inner join canales_pqr c ON canal_id=c.id 
            WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime,?, 120) AND CONVERT(smalldatetime,?, 120)
            group by nombre 

        ";
        $query5="
            WITH pqrs_por_tipo AS (
                SELECT 
                    FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes, 
                    c.nombre AS nombre,
                    COUNT(*) AS pqrs
                FROM 
                    pqrs p
                JOIN 
                    canales_pqr c ON p.canal_id = c.id
                WHERE 
                    p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime, ?, 120)
                GROUP BY 
                    FORMAT(p.fecha_creacion, 'yyyy-MM'),
                    c.nombre
            ),
            totales_por_mes AS (
                SELECT 
                    mes,
                    'Total' AS nombre,
                    SUM(pqrs) AS pqrs
                FROM 
                    pqrs_por_tipo
                GROUP BY 
                    mes
            )

            SELECT mes, nombre, pqrs
            FROM (
                SELECT * FROM pqrs_por_tipo
                UNION ALL
                SELECT * FROM totales_por_mes
            ) AS resultado
            ORDER BY 
                mes ASC, 
                CASE WHEN nombre = 'Total' THEN 'zzz' ELSE nombre END

        ";
        $query6="SELECT COUNT(*) AS pqrs,
                LTRIM(RTRIM(s.nombre)) AS nombre
            FROM pqrs p
            INNER JOIN  sede s ON p.sede_id=s.id 
            WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime, ?, 120)
            GROUP BY nombre 
            ";
        $query7="WITH pqrs_por_tipo AS (
                    SELECT 
                        FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes, 
                        LTRIM(RTRIM(s.nombre)) AS nombre,
                        COUNT(*) AS pqrs
                    FROM 
                        pqrs p
                    JOIN 
                        sede s ON p.sede_id = s.id
                    WHERE 
                        p.fecha_creacion BETWEEN CONVERT(smalldatetime,?, 120) AND CONVERT(smalldatetime, ?, 120)
                    GROUP BY 
                        FORMAT(p.fecha_creacion, 'yyyy-MM'),
                        s.nombre
                ),
                totales_por_mes AS (
                    SELECT 
                        mes,
                        'Total' AS nombre,
                        SUM(pqrs) AS pqrs
                    FROM 
                        pqrs_por_tipo
                    GROUP BY 
                        mes
                )

                SELECT mes, nombre, pqrs
                FROM (
                    SELECT * FROM pqrs_por_tipo
                    UNION ALL
                    SELECT * FROM totales_por_mes
                ) AS resultado
                ORDER BY 
                    mes ASC, 
                    CASE WHEN nombre = 'Total' THEN 'zzz' ELSE nombre END
                ";

        $query8="WITH pqrs_por_sede AS (
                    SELECT 
                        LTRIM(RTRIM(s.nombre)) AS sede,
                        COUNT(*) AS pqrs
                    FROM pqrs p
                    JOIN sede s ON p.sede_id = s.id
                    WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime, ?, 120)
                    GROUP BY s.nombre
                ),
                usuarios_por_sede AS (
                    SELECT 
                        LTRIM(RTRIM(s.nombre)) AS sede,
                        COUNT(DISTINCT c.nro_hist) AS clientes
                    FROM citas c
                    JOIN sede s ON c.sede = s.cod
                    WHERE c.fecha BETWEEN CONVERT(smalldatetime, ?, 120) AND CONVERT(smalldatetime,?, 120)
                    AND c.asistio ='1'
                    GROUP BY s.nombre
                )

                SELECT 
                    s.nombre AS sede,
                    ISNULL(pqrs.pqrs, 0) AS pqrs,
                    ISNULL(users.clientes, 0) AS clientes,
                    ROUND((ISNULL(pqrs.pqrs, 0) * 100.0) / NULLIF(users.clientes, 0), 2) AS porcentaje
                FROM sede s
                LEFT JOIN pqrs_por_sede pqrs ON pqrs.sede = s.nombre
                LEFT JOIN usuarios_por_sede users ON users.sede = s.nombre
                ORDER BY s.nombre;
        ";
        $query9="SELECT 
                FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes, 
                COUNT(*) AS pqrs,
	            'pqr`s' AS nombre
            FROM pqrs p
            WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) 
                                        AND CONVERT(smalldatetime, ?, 120)
            AND (p.tipo_id IN (
                SELECT id FROM tipos_pqr t WHERE t.nombre='Petición' OR t.nombre='queja' OR t.nombre='Reclamo'					  
            ))
            GROUP BY FORMAT(p.fecha_creacion, 'yyyy-MM');
            ";
        $query10="WITH pqr_tendencia AS (
                SELECT 
                    FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes, 
                    COUNT(*) AS pqrs
                FROM pqrs p
                WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
                AND p.tipo_id IN (
                    SELECT id 
                    FROM tipos_pqr t 
                    WHERE t.nombre IN ('Petición', 'Queja', 'Reclamo')
                )
                GROUP BY FORMAT(p.fecha_creacion, 'yyyy-MM')
            ),
            pacientes_atendidos AS (
                SELECT 
                    FORMAT(fecha, 'yyyy-MM') AS mes,
                    COUNT(DISTINCT nro_hist) AS pacientes

                FROM citas 
                WHERE fecha BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
                AND asistio = '1'
                GROUP BY FORMAT(fecha, 'yyyy-MM')
            ),
            valores AS (
                SELECT 
                    pqr.mes,
                    pqr.pqrs,
                    ISNULL(pa.pacientes, 0) AS pacientes,
                    2 AS meta,
                    CASE
                        WHEN pa.pacientes IS NOT NULL THEN ROUND((1.0 * pqr.pqrs) / NULLIF(pa.pacientes, 0) * 100, 2)
                        ELSE 0
                    END AS porcentaje
                FROM pqr_tendencia pqr
                LEFT JOIN pacientes_atendidos pa ON pqr.mes = pa.mes
            )

            SELECT mes, 'pqrs' AS nombre, pqrs AS pqrs FROM valores
            UNION ALL
            SELECT mes, 'pacientes' AS nombre, pacientes AS pqrs FROM valores
            UNION ALL
            SELECT mes, 'porcentaje' AS nombre, porcentaje AS pqrs FROM valores
            UNION ALL 
            SELECT mes,'meta' AS nombre ,meta AS pqrs FROM valores
            ORDER BY mes, nombre;";
        
        $query11="SELECT 
                    sede,
                    SUM(CASE WHEN tipo = 'QUEJA' THEN pqrs ELSE 0 END) AS QUEJA,
                    SUM(CASE WHEN tipo = 'PETICIÓN' THEN pqrs ELSE 0 END) AS PETICION,
                    SUM(CASE WHEN tipo = 'RECLAMO' THEN pqrs ELSE 0 END) AS RECLAMO,
                    SUM(CASE WHEN tipo = 'SUGERENCIA' THEN pqrs ELSE 0 END) AS SUGERENCIA,
                    SUM(CASE WHEN tipo = 'FELICITACIÓN' THEN pqrs ELSE 0 END) AS FELICITACION,
                    SUM(CASE WHEN tipo = 'SOLICITUD' THEN pqrs ELSE 0 END) AS SOLICITUD,
                    SUM(CASE WHEN tipo = 'SOLICITUD DE CITAS' THEN pqrs ELSE 0 END) AS SOLICITUD_DE_CITAS

                FROM (
                    SELECT 
                        t.nombre AS tipo,
                        CASE 
                            WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                            ELSE LTRIM(RTRIM(s.nombre))
                        END AS sede,
                        COUNT(*) AS pqrs
                    FROM pqrs p
                    INNER JOIN tipos_pqr t ON p.tipo_id = t.id
                    INNER JOIN sede s ON p.sede_id = s.id
                    WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?, 120) 
                                            AND CONVERT(smalldatetime, ?, 120)
                    GROUP BY 
                        t.nombre, 
                        CASE 
                            WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                            ELSE LTRIM(RTRIM(s.nombre))
                        END
                ) AS pivot_base
                GROUP BY sede
                ORDER BY sede
            ";
        $query12="SELECT 
                    a.nombre as nombre,
                    count(*) as pqrs
                from pqrs p 
                INNER JOIN sedes_areas_pqrs a ON p.area_id = a.id
                WHERE P.fecha_creacion BETWEEN  CONVERT(smalldatetime, ?, 120) 
                                            AND CONVERT(smalldatetime, ?, 120)
                GROUP BY a.nombre
                ORDER BY a.nombre";
        $query13="SELECT 
                sede,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - ABA' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_ABA,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - CENTRAL DE CITAS' THEN pqrs ELSE 0 END) AS CENTRAL_DE_CITAS,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - DIRECCIÓN O SUBDIRECCIÓN' THEN pqrs ELSE 0 END) AS DIRECCION_SUBDIRECCION,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - EDUCACIÓN' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_EDUCACION,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - EXPERIENCIA SERVICIO AL CLIENTE' THEN pqrs ELSE 0 END) AS EXPERIENCIA_CLIENTE,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - FACTURACIÓN' THEN pqrs ELSE 0 END) AS FACTURACION,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - GUARDA DE SEGURIDAD' THEN pqrs ELSE 0 END) AS SEGURIDAD,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - LOGÍSTICA' THEN pqrs ELSE 0 END) AS LOGISTICA,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - OTRO' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_OTRO,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - PERSONAL ADMISIONISTA' THEN pqrs ELSE 0 END) AS ADMISIONISTA,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - PROYECCIÓN SOCIAL' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_PROYECCION,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - REHABILITACIÓN INTEGRAL' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_REHABILITACION,
                SUM(CASE WHEN area = 'ADMINISTRATIVO - RESPUESTA TELEFÓNICA' THEN pqrs ELSE 0 END) AS RESPUESTA_TELEFONICA,
                SUM(CASE WHEN area = 'ASISTENCIAL - ABA' THEN pqrs ELSE 0 END) AS ASISTENCIAL_ABA,
                SUM(CASE WHEN area = 'ASISTENCIAL - EDUCACIÓN' THEN pqrs ELSE 0 END) AS ASISTENCIAL_EDUCACION,
                SUM(CASE WHEN area = 'ASISTENCIAL - PROYECCIÓN SOCIAL' THEN pqrs ELSE 0 END) AS ASISTENCIAL_PROYECCION,
                SUM(CASE WHEN area = 'ASISTENCIAL - REHABILITACIÓN INTEGRAL' THEN pqrs ELSE 0 END) AS ASISTENCIAL_REHABILITACION,
                SUM(CASE WHEN area = 'SOLICITUD' THEN pqrs ELSE 0 END) AS SOLICITUD
            FROM (
                SELECT 
                    a.nombre AS area,
                    CASE 
                        WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                        ELSE LTRIM(RTRIM(s.nombre))
                    END AS sede,
                    COUNT(*) AS pqrs
                FROM pqrs p
                INNER JOIN sedes_areas_pqrs a ON p.area_id = a.id
                INNER JOIN sede s ON p.sede_id = s.id
                WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?, 120) 
                                        AND CONVERT(smalldatetime,?, 120)
                GROUP BY 
                    a.nombre, 
                    CASE 
                        WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                        ELSE LTRIM(RTRIM(s.nombre))
                    END
            ) AS pivot_base
            GROUP BY sede
            ORDER BY sede;
            ";
        $query14="SELECT 
                    area,
                    SUM(CASE WHEN sede = 'SEDE CENTRAL' THEN pqrs ELSE 0 END) AS SEDE_CENTRAL,
                    SUM(CASE WHEN sede = 'COLEGIO CLL 42' THEN pqrs ELSE 0 END) AS COLEGIO_CLL_42,
                    SUM(CASE WHEN sede = 'SEDE BOLARQUI' THEN pqrs ELSE 0 END) AS SEDE_BOLARQUI,
                    SUM(CASE WHEN area = 'SEDE SAN GIL' THEN pqrs ELSE 0 END) AS SEDE_SAN_GIL

                FROM (
                    SELECT 
                        a.nombre AS area,
                        CASE 
                            WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                            ELSE LTRIM(RTRIM(s.nombre))
                        END AS sede,
                        COUNT(*) AS pqrs
                    FROM pqrs p
                    INNER JOIN sedes_areas_pqrs a ON p.area_id = a.id
                    INNER JOIN sede s ON p.sede_id = s.id
                    WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?, 120) 
                                            AND CONVERT(smalldatetime,?, 120)
                    GROUP BY 
                        a.nombre, 
                        CASE 
                            WHEN LTRIM(RTRIM(s.nombre)) IN ('PRINCIPAL - CRA 27', 'SEDE ABA', 'CRA 26') THEN 'SEDE CENTRAL'
                            ELSE LTRIM(RTRIM(s.nombre))
                        END
                ) AS pivot_base
                GROUP BY area
                ORDER BY area;
                ";
        $query15="SELECT 
            FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes,
            COUNT (*) AS pqrs,
            a.nombre as nombre

        FROM pqrs p 
        INNER JOIN sedes_areas_pqrs a ON p.area_id=a.id
        WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,'2025-01-01',120) AND CONVERT(smalldatetime,'2025-05-26',120)
        GROUP BY a.nombre,FORMAT(p.fecha_creacion, 'yyyy-MM')
        ORDER BY a.nombre
        ";
        $query16="SELECT
                c.nombre AS nombre,
                count(*) AS pqrs
            FROM pqrs p 
            INNER JOIN caracteristicas_sogcs c ON p.sogcs_id = c.id
            WHERE P.fecha_creacion BETWEEN  CONVERT(smalldatetime, ?, 120) 
                                        AND CONVERT(smalldatetime, ?, 120)
            GROUP BY c.nombre
            ORDER BY c.nombre;";
        
        $query17="SELECT 
            caracteristica,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - ABA' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_ABA,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - CENTRAL DE CITAS' THEN pqrs ELSE 0 END) AS CENTRAL_DE_CITAS,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - DIRECCIÓN O SUBDIRECCIÓN' THEN pqrs ELSE 0 END) AS DIRECCION_SUBDIRECCION,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - EDUCACIÓN' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_EDUCACION,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - EXPERIENCIA SERVICIO AL CLIENTE' THEN pqrs ELSE 0 END) AS EXPERIENCIA_CLIENTE,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - FACTURACIÓN' THEN pqrs ELSE 0 END) AS FACTURACION,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - GUARDA DE SEGURIDAD' THEN pqrs ELSE 0 END) AS SEGURIDAD,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - LOGÍSTICA' THEN pqrs ELSE 0 END) AS LOGISTICA,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - OTRO' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_OTRO,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - PERSONAL ADMISIONISTA' THEN pqrs ELSE 0 END) AS ADMISIONISTA,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - PROYECCIÓN SOCIAL' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_PROYECCION,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - REHABILITACIÓN INTEGRAL' THEN pqrs ELSE 0 END) AS ADMINISTRATIVO_REHABILITACION,
            SUM(CASE WHEN area = 'ADMINISTRATIVO - RESPUESTA TELEFÓNICA' THEN pqrs ELSE 0 END) AS RESPUESTA_TELEFONICA,
            SUM(CASE WHEN area = 'ASISTENCIAL - ABA' THEN pqrs ELSE 0 END) AS ASISTENCIAL_ABA,
            SUM(CASE WHEN area = 'ASISTENCIAL - EDUCACIÓN' THEN pqrs ELSE 0 END) AS ASISTENCIAL_EDUCACION,
            SUM(CASE WHEN area = 'ASISTENCIAL - PROYECCIÓN SOCIAL' THEN pqrs ELSE 0 END) AS ASISTENCIAL_PROYECCION,
            SUM(CASE WHEN area = 'ASISTENCIAL - REHABILITACIÓN INTEGRAL' THEN pqrs ELSE 0 END) AS ASISTENCIAL_REHABILITACION,
            SUM(CASE WHEN area = 'SOLICITUD' THEN pqrs ELSE 0 END) AS SOLICITUD
        FROM (
            SELECT 
                a.nombre AS area,
                c.nombre AS caracteristica,
                COUNT(*) AS pqrs
            FROM pqrs p
            INNER JOIN sedes_areas_pqrs a ON p.area_id = a.id
            INNER JOIN caracteristicas_sogcs c ON p.sogcs_id = c.id
            WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?, 120) 
                                    AND CONVERT(smalldatetime,?, 120)
            GROUP BY 
                a.nombre, 
                c.nombre
        ) AS pivot_base
        GROUP BY caracteristica
        ORDER BY caracteristica;";
        $query18="SELECT
                m.nombre AS nombre,
                COUNT(*) AS pqrs
            FROM pqrs p 
            INNER JOIN motivos_pqr m ON p.motivo_id = m.id
            WHERE P.fecha_creacion BETWEEN  CONVERT(smalldatetime,?, 120) 
                                        AND CONVERT(smalldatetime,?, 120)
            GROUP BY m.nombre
            ORDER BY m.nombre;";

        $query19="SELECT
            FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes,
            CAST(AVG(CAST(DATEDIFF(HOUR, p.fecha_creacion, p.fecha_cierre) AS FLOAT)) / 24 AS DECIMAL(10,2)) AS pqrs,
            t.nombre 
        FROM pqrs p
        INNER JOIN tipos_pqr t ON p.tipo_id = t.id
        WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime, '2025-01-01', 120) 
                                AND CONVERT(smalldatetime, '2025-05-28', 120)
        GROUP BY FORMAT(p.fecha_creacion, 'yyyy-MM'), t.nombre
        ORDER BY mes;
        ";
        $query20="SELECT
                FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes,
                CAST(SUM(CASE 
                            WHEN DATEDIFF(HOUR, p.fecha_creacion, p.fecha_cierre) <= t.dias_respuesta * 24 
                            THEN 1 
                            ELSE 0 
                        END) * 100.0 / COUNT(*) AS DECIMAL(5,2)) AS pqrs,
                'Porcentaje Pqrs A tiempo' AS nombre
            FROM pqrs p
            INNER JOIN tipos_pqr t ON p.tipo_id = t.id
            WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) 
                                    AND CONVERT(smalldatetime, ?, 120)
            GROUP BY FORMAT(p.fecha_creacion, 'yyyy-MM')

            UNION ALL

            SELECT
                DISTINCT FORMAT(p.fecha_creacion, 'yyyy-MM') AS mes,
                100.00 AS pqrs,
                'Meta' AS nombre
            FROM pqrs p
            WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime, ?, 120) 
                                    AND CONVERT(smalldatetime, ?, 120)
            ORDER BY mes, nombre;";
        $query21="SELECT 
            a.nombre AS nombre,
            AVG(DATEDIFF(HOUR, fecha_creacion, fecha_cierre)) AS pqrs
            FROM pqrs p
            INNER JOIN sedes_areas_pqrs a ON p.area_id = a.id
            WHERE p.fecha_creacion BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
            GROUP BY a.nombre
            ORDER BY pqrs DESC;";
        
        $dataAnalitycs=[
                    "history"=>self::senqQuery($query1,$bindings),
                    "infoTypes"=>self::senqQuery($query2,$bindings),
                    "infoTypesByDate"=>self::senqQuery($query3,$bindingsTendencie),
                    "infoCanals"=>self::senqQuery($query4,$bindings),
                    "infoCanalsByDate"=>self::senqQuery($query5,$bindingsTendencie),
                    "infoSedes"=>self::senqQuery($query6,$bindings),
                    "infoSedeByDate"=>self::senqQuery($query7,$bindingsTendencie),
                    "infoPorcentsPqrsBySede"=>self::senqQuery($query8,array_merge($bindings,$bindings)),
                    "infoPqrsTendencie"=>self::senqQuery($query9,$bindingsTendencie),
                    "infoPqrsTendencieByUsers"=>self::senqQuery($query10,array_merge($bindingsTendencie,$bindingsTendencie)),
                    "infoPqrsvsSedes"=>self::senqQuery($query11,$bindings),
                    "infoPqrsbyArea"=>self::senqQuery($query12,$bindings),
                    "infoPqrsVsSedesVsAreas"=>self::senqQuery($query13,$bindings),
                    "infoPqrsVsAreasVsSedes"=>self::senqQuery($query14,$bindings),
                    "tendenciePqrsByAreas"=>self::senqQuery($query15,$bindingsTendencie),
                    "infoPqrsByCharacter"=>self::senqQuery($query16,$bindings),
                    "infoPqrsVsCharacterVsArea"=>self::senqQuery($query17,$bindings),
                    "infoPqrsByCause"=>self::senqQuery($query18,$bindings),
                    "tendenceRangeTimeResponsePqrs"=>self::senqQuery($query19,$bindingsTendencie),
                    "tendenciaPqrsOnTime"=>self::senqQuery($query20,array_merge($bindingsTendencie,$bindingsTendencie)),
                    "dataResponseTimeByArea"=>self::senqQuery($query21,$bindings),
                    
                ];
                        
        return $dataAnalitycs;
    }
}