<?php

namespace App\Constants;

class TemplatesQuerys{
    const TEMPLATE_QUERY_CITAS="WITH citas_con_fecha AS (
        SELECT 
                id,
                CAST(fecha AS datetime) + CAST(hora AS time) AS fecha_completa,
                autoriz,
                tiempo,
                nro_hist
            FROM citas
            {{{}}}
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
            CASE
                WHEN ci.fecha_evo_ampliada = '0'
                    AND CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                    AND ci.asistio != '1'
                    AND ci.cancelada != '1'
                THEN '1'
                ELSE '0'
            END AS no_asistida,
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

                    WHEN cif.fecha_completa > GETDATE()
                        THEN '0'


                    WHEN CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                        AND ci.fecha_evo_ampliada = '0'
                        THEN '0'

                    ELSE '1'
                END AS evolucionable,
            CASE 
                WHEN GETDATE() BETWEEN cif.fecha_completa 
                                AND DATEADD(MINUTE, pro.duraccion, cif.fecha_completa) 
                THEN '1'
                ELSE '0'
            END AS actual,
            pro.id_programa,
            pro.multi_evol AS multi_evo,
            pro.consulta

        FROM citas ci
        INNER JOIN cliente cli ON ci.nro_hist = cli.codigo
        INNER JOIN citas_con_fecha cif ON ci.id = cif.id
        INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
        ORDER BY cif.fecha_completa, CAST(ci.hora AS TIME)";

    const TEMPLATE_QUERY_TO_GET_PROFESIONALS_AUTHS="WITH entidades AS (
                SELECT * 
                FROM cliente 
                WHERE socie <> ''
            ),

            autorizaciones AS (
                    SELECT 
                    ci.nro_hist,
                    ci.codent,
                    ci.autoriz
                FROM citas ci
                INNER JOIN autoriza au ON ci.autoriz = au.n_autoriza 
                                        AND ci.nro_hist =au.historia
                                        --AND ci.codent =au.entidad
                {{}}
                AND au.f_vence > GETDATE()
                AND (au.anulada = 0 OR (au.suspendida = 1 AND au.anulada = 1))
                GROUP BY ci.nro_hist,ci.codent,ci.autoriz

            )
            SELECT 
                RTRIM(cli.nombre) AS usuario,
                RTRIM(ent.nombre) AS entidad,
                au1.autoriz ,
                au2.cerrar_ord_asp AS cerrada,
                au2.fecha_cerrar_asp AS fecha_cierre,
                CASE 
                    WHEN au2.usu_cerrar_asp ='HERZON S' THEN 'SISTEMA'
                    ELSE au2.usu_cerrar_asp
                END AS cierre,
                au2.razon_cerrar_orden_asp as razon,
                au2.id,
                au2.entidad AS codigo_entidad,
                au2.historia,
                au2.f_vence,
                au2.procedi AS procedimiento
                FROM autorizaciones au1
                INNER JOIN autoriza au2 ON au1.autoriz = au2.n_autoriza
                                            AND au1.nro_hist =au2.historia
                                        --AND au1.codent =au2.entidad
                INNER JOIN cliente cli  ON au1.nro_hist =cli.codigo
                INNER JOIN entidades ent ON au1.codent = ent.codigo
                ORDER BY f_vence DESC";

    const TEMPLATE_TO_FILTER_APPIMENTS_BY_AUTORIZ="SELECT id FROM citas
			WHERE cedprof = ?
			--AND codent = ?
			AND nro_hist = ?
			AND autoriz = ?
            AND tiempo = ?
            AND fecha > GETDATE()";
}