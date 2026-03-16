<?php

namespace App\Models;

use App\Models\BaseModel;

class CitasModel extends BaseModel
{
    public function getInfoAppoimnetsToClone(string $cedula, string $from, string $to)
    {
        $bindingsQueryGetAvailability = [$cedula, $from, $to];

        $queryGetAvailability = "WITH autorizaciones AS (
            SELECT DISTINCT autoriz, tiempo,nro_hist AS historia
            FROM citas
            WHERE cedprof = ?
            AND fecha BETWEEN CONVERT(smalldatetime,?, 120)
                            AND CONVERT(smalldatetime,?, 120)
        ),
        citas_programadas AS (
            SELECT a.autoriz,
				a.tiempo, COUNT(*) AS total_programadas
            FROM citas c
            INNER JOIN autorizaciones a ON c.autoriz = a.autoriz AND c.tiempo = a.tiempo
			INNER JOIN procedipro pro ON c.procedipro = pro.nombre
            WHERE c.cancelada != '1'
            AND c.na != '1'
			AND pro.sumable = '1'
            AND c.nro_hist = a.historia
            GROUP BY a.autoriz, a.tiempo
        ),
        citas_disponibles AS (
            SELECT au.n_autoriza AS autoriz, 
					au.procedi, 
					au.cantidad
            FROM autoriza au
			INNER JOIN autorizaciones a ON au.n_autoriza = a.autoriz
										AND au.procedi = a.tiempo
										AND au.historia = a.historia
            AND au.cerrar_ord_asp != '1'
			AND au.anulada = '0' 

            UNION ALL
            
			SELECT ad.n_autoriza AS autoriz, 
				   ad.procedi, 
				   ad.cantidad
            FROM autorizad ad
			INNER JOIN autorizaciones a ON ad.n_autoriza = a.autoriz
										AND ad.procedi = a.tiempo
										AND ad.historia = a.historia
			AND ad.anulada = '0' 
        )

        SELECT DISTINCT
            CONCAT(a.autoriz, '|||', a.tiempo) AS autorizacion,
            dis.cantidad,
            ISNULL(cp.total_programadas,0) as total_programadas,
            ISNULL(dis.cantidad, 0) - ISNULL(cp.total_programadas, 0) AS disponibles
        FROM autorizaciones a
        LEFT JOIN citas_programadas cp ON a.autoriz = cp.autoriz AND a.tiempo = cp.tiempo
        LEFT JOIN citas_disponibles dis ON a.autoriz = dis.autoriz AND a.tiempo = dis.procedi
        WHERE ISNULL(dis.cantidad, 0) - ISNULL(cp.total_programadas, 0) > 0";

        $bindingsGetAppointments = [$cedula,$from,$to];

        $queryGetAppointments = "WITH citas_con_fecha AS (
                SELECT 
                    id,
                    CAST(fecha AS datetime) + CAST(hora AS time) AS fecha_completa
                FROM citas
                WHERE 
                    cedprof = ?
                    AND fecha BETWEEN CONVERT(smalldatetime,?, 120) 
                                  AND CONVERT(smalldatetime, ?, 120)
            )
            SELECT 
                c.id,
                c.nro_hist, 
                c.cedprof, 
                c.ced_usu,
                c.registro,
                c.sede,
                c.observaciones_mc,
                c.codent, 
                c.codent2,
                c.tiempo, 
                c.direccion_cita,
                c.procedim,
                c.procedipro,
                c.autoriz, 
                c.fecha, 
                c.hora,
                c.fec_hora,
                c.recordatorio_wsp,
                c.copago,
                cf.fecha_completa
            FROM citas c
            INNER JOIN citas_con_fecha cf ON c.id = cf.id
            INNER JOIN procedipro pro ON c.procedipro = pro.nombre
                                        AND pro.replicable = '1'
            ORDER BY fecha_completa
        ";
        
        $infoSchedule = [
            'availability' => self::senqQuery($queryGetAvailability, $bindingsQueryGetAvailability),
            'appoiments'   => self::senqQuery($queryGetAppointments, $bindingsGetAppointments),
        ];

        return $infoSchedule;
    }
    public function saveAppoimentClone(array $cita){

        $bindings= [
                $cita['nro_hist'],   
                $cita['cedprof'],   
                $cita['ced_usu'],      
                $cita['registro'],
                $cita['sede'],           
                $cita['observaciones_mc'],
                $cita['codent'],           
                $cita['codent2'],
                $cita['tiempo'],  
                $cita['direccion_cita'],    
                $cita["procedim"],
                $cita['procedipro'],
                $cita["autoriz" ],        
                $cita['fecha'],                     
                $cita['hora'],                      
                $cita['fec_hora'],                   
                $cita['recordatorio_wsp'],
                $cita['copago']
            ];
        $query="INSERT INTO citas (
                    nro_hist, cedprof, ced_usu, registro,sede,observaciones_mc, codent, codent2,tiempo,direccion_cita,procedim, procedipro,autoriz, fecha, hora, fec_hora, recordatorio_wsp,copago
                )
                VALUES (?,?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?,  
                    CONVERT(smalldatetime, ?, 120), 
                    ?, 
                    CONVERT(smalldatetime, ?, 120), 
                    ?,?
                )
            ";

        $id=self::senqQuery(query:$query,bindings:$bindings,typeConsult:'insert');
        return $id;

    }
    public function getApoimentByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $query = "
            SELECT 
                ci.id,
                ci.fecha,
                ci.hora AS hora,
                ci.autoriz AS autorizacion,
                ci.tiempo AS tiempo,
                ci.procedipro,
                ci.registro,
                ci.fec_hora,
                asistio AS asistida,
                cancelada,
                na AS no_asistida,
                ci.procedipro AS procedimiento,
                pro.duraccion AS duracion,
                cli.nombre AS usuario
            FROM 
                citas ci
            INNER JOIN 
                cliente cli ON cli.codigo LIKE '%' + ci.nro_hist + '%'
            INNER JOIN 
                procedipro pro ON pro.nombre = ci.procedipro
            WHERE 
                ci.id IN ($placeholders)
            ORDER BY 
                ci.hora ASC;
        ";

        return self::senqQuery(query: $query, bindings: $ids);
    }
    public function deleteCalendarProfesional(string $from,string $to,string $cedulaProfesional){
        $query="DELETE FROM citas WHERE
            fecha BETWEEN CONVERT(smalldatetime,?,120) AND CONVERT(smalldatetime,?,120)
            AND cedprof =?
            AND cancelada != '1'
            AND asistio != '1'
            AND na != '1'";
        $bindings=[$from,$to,$cedulaProfesional];
        return self::senqQuery($query,$bindings,'delete');
    }

}
