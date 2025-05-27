<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Interfaces\AnalitycsRepositoryInterface;


class AnalitycsRepository extends BaseRepository implements AnalitycsRepositoryInterface{
    

    public function pqrsHistory(string $from, string $to): mixed
    {
        $bindings=[$from,$to];
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
                        CONVERT(date, p.fecha_creacion) AS fecha,
                        t.nombre AS nombre,
                        COUNT(*) AS pqrs
                    FROM 
                        pqrs p
                    JOIN 
                        tipos_pqr t ON p.tipo_id = t.id
                    WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime, '2025-04-01', 120) AND CONVERT(smalldatetime, '2025-04-30', 120)
                    GROUP BY 
                        CONVERT(date, p.fecha_creacion),
                        t.nombre
                ),
                totales_por_fecha AS (
                    SELECT 
                        fecha,
                        'Total' AS nombre_sede,
                        SUM(pqrs) AS pqrs
                    FROM 
                        pqrs_por_tipo
                    GROUP BY 
                        fecha
                )

                SELECT fecha, nombre, pqrs
                FROM (
                    SELECT * FROM pqrs_por_tipo
                    UNION ALL
                    SELECT * FROM totales_por_fecha
                ) AS resultado
                ORDER BY 
                    fecha ASC, 
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
            WITH pqrs_por_sede AS (
                SELECT 
                    CONVERT(date, p.fecha_creacion) AS fecha,
                    c.nombre AS nombre,
                    COUNT(*) AS pqrs
                FROM 
                    pqrs p
                JOIN 
                    canales_pqr c ON p.canal_id = c.id
                WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime,?, 120) AND CONVERT(smalldatetime, ?, 120)
                GROUP BY 
                    CONVERT(date, p.fecha_creacion),
                    c.nombre
            ),
            totales_por_fecha AS (
                SELECT 
                    fecha,
                    'Total' AS nombre_sede,
                    SUM(pqrs) AS cantidad_pqrs
                FROM 
                    pqrs_por_sede
                GROUP BY 
                    fecha
            )

            SELECT fecha, nombre,pqrs
            FROM (
                SELECT * FROM pqrs_por_sede
                UNION ALL
                SELECT * FROM totales_por_fecha
            ) AS resultado
            ORDER BY 
                fecha ASC, 
                CASE WHEN nombre = 'Total' THEN 'zzz' ELSE nombre END;

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
                        CONVERT(date, p.fecha_creacion) AS fecha,
                        LTRIM(RTRIM(s.nombre)) AS nombre,
                        COUNT(*) AS pqrs
                    FROM 
                        pqrs p
                    JOIN 
                        sede s ON p.sede_id = s.id
                    WHERE p.fecha_creacion BETWEEN  CONVERT(smalldatetime,?, 120) AND CONVERT(smalldatetime,?, 120)
                    GROUP BY 
                        CONVERT(date, p.fecha_creacion),
                        s.nombre
                ),
                totales_por_fecha AS (
                    SELECT 
                        fecha,
                        'Total' AS nombre_sede,
                        SUM(pqrs) AS pqrs
                    FROM 
                        pqrs_por_tipo
                    GROUP BY 
                        fecha
                )

                SELECT fecha, nombre, pqrs
                FROM (
                    SELECT * FROM pqrs_por_tipo
                    UNION ALL
                    SELECT * FROM totales_por_fecha
                ) AS resultado
                ORDER BY 
                    fecha ASC, 
                    CASE WHEN nombre = 'Total' THEN 'zzz' ELSE nombre END;
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
        
        
        
        $dataAnalitycs=[
                    "history"=>self::senqQuery($query1,$bindings),
                    "infoTypes"=>self::senqQuery($query2,$bindings),
                    "infoTypesByDate"=>self::senqQuery($query3,$bindings),
                    "infoCanals"=>self::senqQuery($query4,$bindings),
                    "infoCanalsByDate"=>self::senqQuery($query5,$bindings),
                    "infoSedes"=>self::senqQuery($query6,$bindings),
                    "infoSedeByDate"=>self::senqQuery($query7,$bindings),
                    "infoPorcentsPqrsBySede"=>self::senqQuery($query8,array_merge($bindings,$bindings))
                ];
                        
        return $dataAnalitycs;
    }
}