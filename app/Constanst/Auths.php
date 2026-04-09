<?php

namespace App\Constanst;



class Auths {
    const BASE_GET_AUTHS_QUERY = "WITH autorizaciones AS (
                            SELECT DISTINCT
                                a.n_autoriza,
                                a.fecha,
                                a.f_vence,
                                a.f_inicial,
                                a.entidad        AS codent,
                                a.paquete        AS codent2,
                                RTRIM(a.observa) AS observa,
                                a.entidad,
                                a.paquete,
                                a.historia,
                                a.dias
                            FROM autoriza a
                            WHERE 1=1
                            {{}}
                        ),
            params AS (
                SELECT TOP 1 historia FROM autorizaciones
            ),
            procedimientos AS (
                SELECT
                    au.n_autoriza,
                    au.procedi        AS tiempo,
                    RTRIM(pr.descrip)        AS procedim,
                    au.cantidad,
                    au.cerrar_ord_asp AS cerrado,
                    RTRIM(es.Descripcion)    AS especialidad
                FROM autoriza au
                INNER JOIN procdent pr 
                    ON au.procedi = pr.codigo
                LEFT JOIN especialidadAsp es 
                    ON es.id = pr.especialidadAsp
                INNER JOIN autorizaciones a 
                    ON a.n_autoriza = au.n_autoriza
                CROSS JOIN params p
                WHERE au.anulada = '0'
                AND au.historia = p.historia
                AND pr.cod_enti = (
                        SELECT TOP 1 en.tarifa
                        FROM entidades en
                        WHERE en.admini = au.entidad
                )

                UNION ALL

                SELECT
                    ad.n_autoriza,
                    ad.procedi     AS tiempo,
                    RTRIM(ad.nombre)      AS procedim,
                    ad.cantidad,
                    '0'            AS cerrado,
                    RTRIM(es.Descripcion) AS especialidad
                FROM autorizad ad
                INNER JOIN procdent pr 
                    ON ad.procedi = pr.codigo
                LEFT JOIN especialidadAsp es 
                    ON es.id = pr.especialidadAsp
                INNER JOIN autorizaciones a 
                    ON a.n_autoriza = ad.n_autoriza
                WHERE ad.anulada = '0'
            ),

            contador AS (
                SELECT
                    p.n_autoriza,
                    p.tiempo,
                    COUNT(*) AS total_programadas
                FROM procedimientos p
                INNER JOIN citas c 
                    ON c.tiempo  = p.tiempo
                AND c.autoriz = p.n_autoriza
                CROSS JOIN params pa
                WHERE c.cancelada <> '1'
                AND c.na        <> '1'
                AND c.nro_hist  = pa.historia
                AND NOT EXISTS (
                        SELECT 1
                        FROM procedipro pp
                        WHERE pp.nombre  = c.procedipro
                        AND pp.sumable = '0'
                )
                GROUP BY p.n_autoriza, p.tiempo
            ),

            eps AS (
                SELECT *
                FROM cliente
                WHERE socie <> ''
            ),

            citas_por_autoriz AS (
                SELECT autoriz, COUNT(*) AS total
                FROM citas
                GROUP BY autoriz
            )

            SELECT
                p.n_autoriza,
                p.tiempo,
                p.procedim,
                p.cantidad,
                p.cerrado,
                ISNULL(p.especialidad, 'No Encontrada') AS especialidad,
                a.f_vence,
                a.f_inicial,
                a.observa,
                a.paquete        AS convenio,
                a.entidad        AS cod_entidad,
                ISNULL(c.total_programadas, 0) AS total_programadas,
                p.cantidad - ISNULL(c.total_programadas, 0) AS disponibles,
                RTRIM(e.nombre)         AS entidad,
                CASE 
                    WHEN ISNULL(ca.total, 0) > 0 THEN '0'
                    ELSE '1'
                END AS nueva,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM ven_det vd
                        WHERE vd.autoriz = a.n_autoriza
                        AND vd.codigo = a.historia
                        AND vd.entidad = a.entidad
                        AND vd.detalle = ''
                        AND NOT EXISTS (
                            SELECT 1
                            FROM nota_det nt
                            WHERE nt.factura = vd.nro_fact
                        )
                    ) THEN 1
                    ELSE 0
                END AS facturada,
                a.historia,
                RTRIM(en.clase) AS n_convenio,
                a.dias

            FROM procedimientos p
            LEFT JOIN contador c
                ON c.n_autoriza = p.n_autoriza
                AND c.tiempo     = p.tiempo
            INNER JOIN autorizaciones a
                    ON a.n_autoriza = p.n_autoriza
            INNER JOIN eps e
                    ON e.codigo = a.entidad
            LEFT JOIN citas_por_autoriz ca
                ON ca.autoriz = a.n_autoriza
            INNER JOIN entidades en 
                ON en.codigo = a.paquete
            ORDER BY a.f_inicial, a.f_vence;";

    const TEMPLATE_GET_TRAZABILITY = "WITH autorizaciones AS (
            SELECT DISTINCT 
                a.n_autoriza,
                a.fecha,
                a.f_vence,
                a.f_inicial,
                a.entidad AS codent,
                a.paquete AS codent2,
                a.f_registro AS fecha_creacion,
                a.dias,
                a.usuario AS creador,
                RTRIM(a.observa) AS observa,
                a.historia,
                RTRIM(cli.nombre) AS entidad,
                RTRIM(ent.clase) AS convenio,
                ent.tarifa
            FROM autoriza a
            INNER JOIN cliente cli 
                ON cli.codigo = a.entidad
            INNER JOIN entidades ent 
                ON ent.codigo = a.paquete
            --WHERE a.n_autoriza = '16511713'
            --AND a.historia = '0000029622'
            WHERE 1=1
            {{}}
        ),

        factura AS (
            SELECT TOP 1 
                vd.nro_fact,
                vd.fecha,
                vd.autoriz,
                vd.codigo
            FROM ven_det vd
            INNER JOIN autorizaciones au 
                ON vd.autoriz = au.n_autoriza
                AND vd.codigo = au.historia
            WHERE vd.detalle = ''
        ),

        auditoria AS (
            SELECT TOP 1 
                au.fecha,
                RTRIM(em.enombre) AS facturador,
                au.nro
            FROM audi_sis au
            INNER JOIN factura f 
                ON au.nro = f.nro_fact
            LEFT JOIN emplea em 
                ON em.ecc = au.usuario
            WHERE au.opcion = 'FACT. ENTIDADES'
        )

        SELECT 
            au.*,
            f.nro_fact AS factura,
            ad.*,
            nt.fecha as fecha_nota

        FROM autorizaciones au

        LEFT JOIN factura f 
            ON f.autoriz = au.n_autoriza
            AND f.codigo = au.historia
        LEFT JOIN nota_det nt ON nt.factura = f.nro_fact
        LEFT JOIN auditoria ad 
            ON ad.nro = f.nro_fact;
    ";

    const TEMPLATE_GET_TRAZA_ORDERS = " SELECT DISTINCT 
        RTRIM(a.procedi) as codigo_cup,
		a.cerrar_ord_asp,
		a.usu_cerrar_asp,
		a.fecha_cerrar_asp,
		RTRIM(a.razon_cerrar_orden_asp) AS razon_cerrar_orden_asp,
		RTRIM(pr.descrip) AS procedimiento,
		a.cantidad
    FROM autoriza a
	INNER JOIN procdent pr 
    ON a.procedi = pr.codigo
		AND pr.cod_enti = (
				SELECT TOP 1 en.tarifa
				FROM entidades en
				WHERE en.admini = a.entidad
		)
    WHERE 1=1
    {{}}";
    const TEMPLATE_GET_INFO_APPOS_AUTH = "SELECT count(*) AS citas,
        ci.fecha,
        ci.asistio,
        ci.cancelada,
        ci.na,
        RTRIM(ci.procedipro) AS procedipro,
        RTRIM(em.enombre) as profesional,
        RTRIM(ci.direccion_cita) AS direccion_cita,
        pro.sumable,
        ci.tiempo

    FROM citas ci 
    INNER JOIN emplea em ON em.ecc = ci.cedprof
    INNER JOIN procedipro pro ON ci.procedipro = pro.nombre
    WHERE 1=1
    {{}}

    GROUP by ci.fecha,
        ci.asistio,
        ci.cancelada,
        ci.na,
        ci.tiempo,
        ci.procedipro,
        em.enombre,
        ci.direccion_cita,
        pro.sumable
        order by ci.fecha;";
}