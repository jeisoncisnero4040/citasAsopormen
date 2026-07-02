<?php


namespace App\Repositories;


use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Interfaces\CitasRepositoryInterface;
use stdClass;
use Illuminate\Support\Facades\DB;
use App\Constanst\Citas;
use Exception;

class CitasRepository extends BaseRepository implements CitasRepositoryInterface{


    public function create(array $ApposDto): void
    {
        if (empty($ApposDto)) {
            return;
        }

        DB::beginTransaction();

        try {

            foreach (array_chunk($ApposDto, 20) as $chunk) {

                $firstAppo = $chunk[0];
                $query = $this->buildCreateQuery(table:'citas', Exampledata:$firstAppo->toPersistenceArray(), numRegistry: count($chunk));
                $bindings = [];
                foreach ($chunk as $appo) {
                    $bindings = [...$bindings, ...$this->makeValues($appo->toPersistenceArray())];
                }
                DB::insert(query: $query, bindings: $bindings);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    public function getApposAvaiables(string $autoriz, string $cupCode,string $history):stdClass{
        $query="WITH CantidadAutorizada AS (
                SELECT cantidad FROM autoriza WHERE n_autoriza = ?
                AND procedi = ?

                UNION ALL 

                SELECT cantidad FROM autorizad WHERE n_autoriza = ?
                AND procedi = ?
            ),
            CitasProgramadas AS (
                SELECT COUNT(*) AS programadas  
                FROM citas ci
                INNER JOIN procedipro pro ON ci.procedipro = pro.nombre
                WHERE ci.autoriz = ?
                AND ci.tiempo = ?
                AND ci.cancelada <> '1'
                AND pro.sumable = '1'
                AND ci.na <> '1'
                AND ci.nro_hist = ?
            )
            SELECT 
                CASE
                    WHEN ca.cantidad - cp.programadas  >= 0 
                        THEN ca.cantidad - cp.programadas 
                    ELSE 0
                END  AS disponibles
            FROM CantidadAutorizada ca
            CROSS JOIN CitasProgramadas cp";
        
        $bindings=[$autoriz,$cupCode,$autoriz,$cupCode,$autoriz,$cupCode,$history];
        return self::sendQuery(query:$query,bindings:$bindings)[0];
    }
    public function getFamilyAppos(string $idFamiliy):array{
        $where = "WHERE grupo_citas = ? ";
        $query = str_replace('{{WHERE}}',$where,Citas::BASE_GET_APPOS_QUEY);
        return self::sendQuery(query:$query,bindings:[$idFamiliy]);
    }
    public function deleteByIds(array $ids):int{
        $placeholders = self::makePlaceholdersPlains($ids);
        $query="DELETE FROM citas WHERE id IN ($placeholders) AND cancelada = '0' AND asistio = '0' AND na = '0'";
        return self::sendQuery(query:$query,bindings:$ids,typeConsult:'delete');
                    
    }
    public function getApposByIds(array $ids){
        $placeholders = self::makePlaceholdersPlains($ids);
        $where = "WHERE id IN ($placeholders)";
        $query = str_replace('{{WHERE}}',$where,Citas::BASE_GET_APPOS_QUEY);
        return self::sendQuery(query:$query,bindings:$ids);
    }
    public function getById(int $id){
        $query="SELECT 
                        ci.id,
                        ci.fecha,
                        ci.hora AS hora,
                        ci.fec_hora AS hora_asignacion,
                        ci.procedim AS procedimiento,
                        ci.asistio AS asistida,
                        ci.cancelada AS cancelada,
                        ci.na AS no_asistida,
                        ci.autoriz,
                        ci.tiempo AS orden,
                        ci.copago,
                        RTRIM(ci.mean_cancel) AS mean_cancel,
                        pro.duraccion AS duracion,
                        ci.direccion_cita AS direcion,
                        cli.nombre AS usuario,
                        em.enombre AS profesional,
                        oc.contenido AS observaciones,
                        ci.regobserva   
                    FROM 
                        citas ci
                    INNER JOIN 
                        cliente cli ON cli.codigo LIKE '%' + ci.nro_hist + '%'
                    INNER JOIN 
                        procedipro pro ON pro.nombre = ci.procedipro
                    INNER JOIN 
                        emplea em ON em.ecc = ci.cedprof
                    LEFT JOIN  
                        observa_citas oc ON oc.id = CAST(ci.observaciones_mc AS INT)
                    WHERE 
                        ci.id = ?";
        return self::sendQuery(query:$query,bindings:[$id]);
    }
    public function restartAppoiment(int $id):int{
        $query="UPDATE citas SET cancelada = '0', asistio = '0', na = '0',realizar = '' WHERE id = ? AND cancelada = '1'";
        return self::sendQuery(query:$query,bindings:[$id],typeConsult:'update');
    }
    public function validateHistAuth(
        string $history,
        string $n_autoriz
    ): bool {

        $query = "SELECT 1 AS existe
            WHERE EXISTS (
                SELECT 1 FROM autoriza
                WHERE historia = ? AND n_autoriza = ?

                UNION ALL

                SELECT 1 FROM autorizad
                WHERE historia = ? AND n_autoriza = ?
            )
        ";

        $result = self::sendQuery(
            query: $query,
            bindings: [$history, $n_autoriz, $history, $n_autoriz]
        );

        return !empty($result);
    }
    public function getLigtCalendar(string $clientCode, string $from, string $to):array{
        $query="SELECT 
                    CAST(ci.fecha AS datetime) + CAST(ci.hora AS time) AS fecha_inicio,
                    DATEADD(MINUTE, pro.duraccion, CAST(ci.fecha AS datetime) + CAST(ci.hora AS time)) AS hora_fin,
                    ci.procedipro

                FROM 
                    citas ci 
                INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
                WHERE 
                    ci.nro_hist = ? 
                    AND ci.fecha  BETWEEN ?  AND ? 
                    AND ci.cancelada <> '1'
                    AND ci.na <> '1'
                    
                ORDER BY ci.fecha, ci.hora";
                    

        return self::sendQuery(query:$query,bindings:[$clientCode,$from,$to]);
    }
}