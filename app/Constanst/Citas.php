<?php

namespace App\Constanst;

class Citas {
    const BASE_GET_APPOS_QUEY="WITH citas_con_fecha AS (
                    SELECT 
                            id,
                            CAST(fecha AS datetime) + CAST(hora AS time) AS fecha_completa,
                            autoriz,
                            tiempo,
                            nro_hist
                        FROM citas
                        {{WHERE}}
                    )
                    SELECT
                        cif.id, 
                        cif.fecha_completa,
                        DATEADD(MINUTE, pro.duraccion, cif.fecha_completa) AS hora_fin,
                        cif.nro_hist,
                        cif.tiempo,
                        ci.hora,
                        RTRIM(cli.nombre) AS usuario,
                        DATEDIFF(YEAR, cli.f_nacio, GETDATE()) AS edad,
                        ci.procedipro AS procedimiento,
                        ci.autoriz AS autorizacion,
                        ci.asistio,
                        ci.cancelada,
                        ci.procedim,
                        ci.grupo_citas as familia,
                        RTRIM(em.enombre) AS profesional,
                        ci.direccion_cita AS direccion,
                        ci.registro,
                        ci.fec_hora,
                        ci.na AS no_asistida,
                        pro.tipo_evolucion,
                        ci.realizar AS razon_cancelamiento,
                        CONVERT(VARCHAR(5), DATEADD(MINUTE, pro.duraccion, cif.fecha_completa), 108)
                            + CASE 
                                WHEN DATEPART(HOUR, DATEADD(MINUTE, pro.duraccion, cif.fecha_completa)) >= 12 
                                    THEN ' PM'
                                ELSE ' AM'
                            END AS fin,
                        CASE
                            WHEN ci.asistio = '1' OR ci.cancelada = '1'
                                THEN '0'
                            WHEN ci.fecha_evo_ampliada = '1'
                                AND CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                                THEN '1'
                            ELSE '0'
                        END AS evolucionable,

                        pro.id_programa,
                        pro.multi_evol AS multi_evo,
                        pro.consulta,
                        pro.duraccion AS duracion

                    FROM citas ci
                    INNER JOIN cliente cli ON ci.nro_hist = cli.codigo
                    INNER JOIN citas_con_fecha cif ON ci.id = cif.id
                    INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
                    INNER JOIN emplea em ON em.ecc = ci.cedprof
                    ORDER BY cif.fecha_completa, CAST(ci.hora AS TIME)";
}