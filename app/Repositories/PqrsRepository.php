<?php
namespace App\Repositories;
use App\Interfaces\PqrRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PqrsRepository extends BaseRepository implements PqrRepositoryInterface{

    protected $table;
    public function __construct() {
        $this->table = 'pqrs';
    }
    public function create(array $newPqrData): mixed
    {
        $placeholders = self::makePlaceholders(data: $newPqrData);
        $columns = self::makeColumns(data: $newPqrData);
        $bindings = self::makeValues(data: $newPqrData);
    
        // Consulta con subconsulta simplificada
        $query = "
                INSERT INTO {$this->table} ($columns, referencia)
                VALUES (
                    $placeholders,
                    (
                        SELECT CONCAT(
                            UPPER(LEFT(tp.nombre, 1)), '_',
                            (SELECT COUNT(*) FROM {$this->table} WHERE tipo_id = tp.id) + 1, '_',
                            FORMAT(GETDATE(), 'yyyyMMdd')
                        )
                        FROM tipos_pqr tp
                        WHERE tp.id = ?
                    )
                )
            ";
    

        $bindings[] = $newPqrData['tipo_id'];
    
        return self::senqQuery(query: $query, bindings: $bindings, typeConsult: 'insert');
    }
    
    
    public function find(int $pqrId, ?string $estado = null): mixed
    {
        $bindings = [$pqrId,$pqrId];
        $query = "SELECT 
                p.id,
                p.descripcion,
                p.fecha_creacion,
                p.estado,

                -- Datos del usuario
                p.nombre_quien_registra,
                p.identificacion_usuario,
                p.celular_usuario,
                p.email_usuario,
                p.nombre_usuario,
                u.nombre AS tipo_usuario,

                -- Referencia y tiempos
                p.referencia,
                p.fecha_envio_area,
                p.fecha_respuesta,
                p.respuesta,
                p.causas,
                p.usuario_respuesta_area,
                p.medio_respuesta_usuario,
                p.fecha_respuesta_usuario,
                p.dias_oportunidad_res_area AS horas_oportunidad_respuesta_area,
                p.dias_aportunidad_res_calidad,

                -- Canales, tipos, áreas, sogcs
                c.nombre AS canal,
                t.nombre AS tipo_pqr,
                a.nombre AS area_servicio,
                a.nombre_coordinador as cordinador_area,
                a.celular_coordinador,
                a.email_cordinador,
                s.nombre AS sogcs,

                -- Jerarquía de motivos
                mm.nombre AS macromotivo,
                mg.nombre AS motivo_general,
                me.nombre AS motivo_especifico,
                mt.nombre AS tipo_motivo,
                mc.nombre AS causa_motivo

            FROM pqrs p
            LEFT JOIN canales_pqr c ON p.canal_id = c.id
            LEFT JOIN tipos_pqr t ON p.tipo_id = t.id
            LEFT JOIN sedes_areas_pqrs a ON p.area_id = a.id
            LEFT JOIN caracteristicas_sogcs s ON p.sogcs_id = s.id
            LEFT JOIN usuarios_pqrs u ON p.tipo_usuario = u.id

            -- Jerarquía de motivos
            LEFT JOIN motivos_pqr mc ON p.motivo_id = mc.id AND mc.nivel = 'causa'
            LEFT JOIN motivos_pqr mt ON mc.parent_id = mt.id AND mt.nivel = 'tipo'
            LEFT JOIN motivos_pqr me ON mt.parent_id = me.id AND me.nivel = 'especifico'
            LEFT JOIN motivos_pqr mg ON me.parent_id = mg.id AND mg.nivel = 'general'
            LEFT JOIN motivos_pqr mm ON mg.parent_id = mm.id AND mm.nivel = 'macromotivo'
            WHERE p.id = ?

        ";
    
        if ($estado) {
            $bindings[] = $estado;
            $query .= " AND p.estado = ?";
        }
    
        return self::senqQuery(query: $query, bindings: $bindings);
    }
    public function getPqrs(?string $estado=null):mixed
    {
        $bindings = [];
        $query = "SELECT 
                p.id,
                p.descripcion,
                p.fecha_creacion,
                p.estado,

                -- Datos del usuario
                p.nombre_quien_registra,
                p.identificacion_usuario,
                p.celular_usuario,
                p.email_usuario,
                p.nombre_usuario,
                u.nombre AS tipo_usuario,

                -- Referencia y tiempos
                p.referencia,
                p.fecha_envio_area,
                p.fecha_respuesta,
                p.respuesta,
                p.causas,
                p.usuario_respuesta_area,
                p.medio_respuesta_usuario,
                p.fecha_respuesta_usuario,
                p.dias_oportunidad_res_area AS horas_oportunidad_respuesta_area,
                p.dias_aportunidad_res_calidad,
                p.causas,
                p.usuario_respuesta_area,
                p.url_respuesta,
                p.fecha_cierre as fecha_de_cierre,

                -- Canales, tipos, áreas, sogcs
                c.nombre AS canal,
                t.nombre AS tipo_pqr,
                a.nombre AS area_servicio,
                a.nombre_coordinador as cordinador_area,
                a.celular_coordinador,
                a.email_cordinador,
                s.nombre AS sogcs,

                -- Jerarquía de motivos
                mm.nombre AS macromotivo,
                mg.nombre AS motivo_general,
                me.nombre AS motivo_especifico,
                mt.nombre AS tipo_motivo,
                mc.nombre AS causa_motivo

            FROM pqrs p
            LEFT JOIN canales_pqr c ON p.canal_id = c.id
            LEFT JOIN tipos_pqr t ON p.tipo_id = t.id
            LEFT JOIN sedes_areas_pqrs a ON p.area_id = a.id
            LEFT JOIN caracteristicas_sogcs s ON p.sogcs_id = s.id
            LEFT JOIN usuarios_pqrs u ON p.tipo_usuario = u.id

            -- Jerarquía de motivos
            LEFT JOIN motivos_pqr mc ON p.motivo_id = mc.id AND mc.nivel = 'causa'
            LEFT JOIN motivos_pqr mt ON mc.parent_id = mt.id AND mt.nivel = 'tipo'
            LEFT JOIN motivos_pqr me ON mt.parent_id = me.id AND me.nivel = 'especifico'
            LEFT JOIN motivos_pqr mg ON me.parent_id = mg.id AND mg.nivel = 'general'
            LEFT JOIN motivos_pqr mm ON mg.parent_id = mm.id AND mm.nivel = 'macromotivo'

        ";
    
        if ($estado) {
            $bindings[] = $estado;
            $query .= " WHERE p.estado = ?";
        }else{
            $bindings[] = $estado;
            $query .= " WHERE p.estado != 'cerrado'";
        }
    
        return self::senqQuery(query: $query, bindings: $bindings);
    }
    public function remitPqrsToArea(int $idPqrs): mixed{
        $bindings=[$idPqrs];
        $query="UPDATE pqrs 
                SET 
                    fecha_envio_area = GETDATE(),
                    dias_oportunidad_res_area = 36,
                    estado = 'en area'
                WHERE id = ?";
        return self::senqQuery($query,$bindings,'update');
    }
    public function saveAnswerAreaPqrs(array $request, array $actionsWithUrls, int $id): mixed
    {
        $setClause = self::makeSetClause($request);
        $query1 = "UPDATE {$this->table} SET {$setClause}, estado = ?, fecha_respuesta = GETDATE() WHERE id = ?";
        
        $values = self::makeValues($request);
        $bindingsQuery1 = array_merge($values, ['area respondido', $id]);
    
        $columns = self::makeColumns($actionsWithUrls[0]);
        $placeholders = implode(', ', array_fill(0, count($actionsWithUrls[0]) + 1, '?'));
        $valuesPlaceholder = implode(', ', array_fill(0, count($actionsWithUrls), "($placeholders)"));
        $query2 = "INSERT INTO acciones_pqrs ({$columns}, id_pqrs) VALUES {$valuesPlaceholder}";
    
        $bindingsQuery2 = self::makeBindingsInsertActionsPqrs($actionsWithUrls, $id);
    
        $pqrsUpdated = 0;  
    
        DB::transaction(function () use ($query1, $query2, $bindingsQuery1, $bindingsQuery2, &$pqrsUpdated) {
            $pqrsUpdated = self::senqQuery($query1, $bindingsQuery1, 'update');
    
            if ($pqrsUpdated !== 0) {
                self::senqQuery($query2, $bindingsQuery2, 'insert');
            }
        });
    
        return $pqrsUpdated;
    }
    public function getActionsPqrs(int $idPqrs):mixed{
        $query="SELECT * FROM acciones_pqrs WHERE id_pqrs = ?";
        $bindings=[$idPqrs];
        return self::senqQuery(query:$query,bindings:$bindings);
    }
    public function changueAreaPqrs(int $pqrsId,array $newAreaData):mixed{
        $setClauseQuery1=self::makeSetClause($newAreaData);
        $bindingsQuery1=self::makeValues($newAreaData);
        $bindingsQuery1Final=array_merge($bindingsQuery1,[$pqrsId]);
        $query1="UPDATE  {$this->table} SET {$setClauseQuery1},
                estado = 'activa',
                respuesta=NULL,
                causas=NULL,
                usuario_respuesta_area=NULL,
                fecha_respuesta = NULL
                WHERE ID = ?
                ";
        $query2="SELECT url_evidencia FROM acciones_pqrs WHERE id_pqrs = ? AND url_evidencia != ''";
        $bindings=[$pqrsId];

        $query3="DELETE FROM acciones_pqrs WHERE id_pqrs = ? ";
        $bindings=[$pqrsId];

        $urlToDeleteStorage = [];  
        DB::transaction(function () use ($query1, $query2,$query3, $bindingsQuery1Final,$bindings , &$urlToDeleteStorage) {
            self::senqQuery($query1,$bindingsQuery1Final,'update');
            $urlToDeleteStorage=self::senqQuery($query2,$bindings);
            self::senqQuery($query3,$bindings,'delete');
        });
        return $urlToDeleteStorage;

    }
    public function saveAnswerToUser(int $pqrsId, $request): mixed {
        $setClause = self::makeSetClause($request);
        $bindings = self::makeValues($request);

        $query = "UPDATE {$this->table} SET 
                $setClause, 
                estado = 'respondido', 
                fecha_respuesta_usuario = GETDATE()
                WHERE id = ?";

        return self::senqQuery($query, array_merge($bindings, [$pqrsId]), 'update');
    }
    public function closePqrs(int $pqrsId):mixed{
        $bindings=[$pqrsId];
        $query="UPDATE {$this->table} SET estado = 'cerrado', fecha_cierre= GETDATE() WHERE id = ?";
        return self::senqQuery($query,$bindings,'update');
    }
}