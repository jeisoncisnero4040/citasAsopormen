<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mappers\CalendarProfesionalMapper;
use App\Models\ScheduleModel;
use Illuminate\Support\Facades\DB;
use App\utils\ResponseManager;
use Exception;
use Carbon\Carbon;
use Doctrine\DBAL\Query;

class ProfesionalService{
    private $responseManager;
    public function __construct(ResponseManager $responseManager)
    {
        $this->responseManager=$responseManager;

    } 
    public function searchProfesionalByString($string){
        $string = trim($string);
        if (empty($string)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }
        

        $searchName = strtoupper($string);

        $profesionals = DB::select("SELECT TOP 20 emp.ecc, emp.enombre, esp.nombre
            FROM emplea emp
            INNER JOIN especial esp ON emp.especialidad = esp.cod
            INNER JOIN usuarios usu ON emp.ecc = usu.cedula
            WHERE emp.agenda = 1 
            AND UPPER(emp.enombre) LIKE ?
            AND estado = 'ACTIVO'
            ORDER BY emp.enombre
        ", ["%{$searchName}%"]);

        if (empty($profesionals)) {
            throw new NotFoundException("no se han encontado registros",404);
        }

        return  $this->responseManager->success($profesionals);
         
    }
    public function getCaledarByProfesionalCedula($cedula){
        $this->checkCedula($cedula);
        $unMappedProfesionalCalendar=$this->makeQuery($cedula);
        $profesionalCalendar=$this->mapCalendar($unMappedProfesionalCalendar);
        return $this->responseManager->success($profesionalCalendar);
    }

    public function getCalendarPro(array $request){
        $cedula=$request['cedula'];
        $from=Carbon::parse($request['from'])->format('Y-m-d');
        $to=Carbon::parse($request['to'])->format('Y-m-d');
        $calendar=$this->makeQueryToGetCalendar($cedula,$from,$to);
        if(empty($calendar)){
            throw new NotFoundException("El Profesional no registra citas en este periodo de Tiempo",404);
        }
        return $this->responseManager->success($calendar);
    }
    
    
    private function checkCedula($cedula) {
        if (empty($cedula)) {
            throw new BadRequestException("Cédula no puede ser nula",400);
        }

    }
    private function makeQuery($cedula){
        try{
            $profesionalCalendar=DB::select("
                SELECT 
                    ci.id,
                    ci.fecha,
                    ci.hora AS hora,
                    ci.tiempo AS tiempo,
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
                INNER JOIN procedipro pro ON pro.nombre =ci.procedipro
                WHERE 
                    ci.cedprof = ?
                    AND ci.fecha > GETDATE()
                    AND ci.cancelada='0'
                    AND ci.asistio='0'
                    AND ci.na='0'
                ORDER BY 
                    ci.hora ASC;

            ",[$cedula]);
            return $profesionalCalendar;
        }catch(\Exception $e ){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function makeQueryToGetCalendar($cedula,$from,$to){
        try{
            $profesionalCalendar=DB::select("WITH citas_con_fecha AS (
                    SELECT 
                            id,
                            CAST(fecha AS datetime) + CAST(hora AS time) AS fecha_completa,
                            autoriz,
                            tiempo,
                            nro_hist
                        FROM citas
                        WHERE cedprof = ?
                            AND fecha BETWEEN CONVERT(smalldatetime, ?, 120) 
                            AND CONVERT(smalldatetime, ?, 120)
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
                        ci.direccion_cita AS direccion,
                        ci.registro,
                        ci.fec_hora,
                        ci.procedim,
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
                            WHEN ci.fecha_evo_ampliada = '1'
                                AND CAST(cif.fecha_completa AS date) < CAST(GETDATE() AS date)
                                THEN '1'
                            ELSE '0'
                        END AS evolucionable,
                        pro.id_programa,
                        pro.multi_evol AS multi_evo,
                        pro.consulta,
                        pro.duraccion as duracion

                    FROM citas ci
                    INNER JOIN cliente cli ON ci.nro_hist = cli.codigo
                    INNER JOIN citas_con_fecha cif ON ci.id = cif.id
                    INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
                    ORDER BY cif.fecha_completa, CAST(ci.hora AS TIME)",[$cedula,$from,$to]);
                return $profesionalCalendar;
            }catch(\Exception $e ){
                throw new ServerErrorException($e->getMessage(),500);
            }
    }
    public function getProcediprosByProCedula(array $request){
        $cedula=$request['cedula'];
        $codeCup=$request['cup'];
        $entidad=$request['entidad'];
        $procedipros=$this->sendQueryToGetProcedims($cedula,$codeCup,$entidad);
        if(empty($procedipros)){
            throw new BadRequestException("El Profesional no registra procedimientos activos para el procedimiento seleccionado y/o para la entidad seleccionada",404);
        }
        return $this->responseManager->success(
            $procedipros
        );

    }
    private function sendQueryToGetProcedims(string $cedula, string $cupCode,string $entidad): array
    {
        try {
            $query = "WITH procedimientos_cup AS (
                    SELECT pro.nombre
                    FROM procedipro pro
                    INNER JOIN procdent ent 
                        ON ent.especialidadAsp = pro.especialidadAsp
                    WHERE ent.codigo = ?
                ),
                procedimientos_profesional AS (
                    SELECT DISTINCT
                        procedipro
                    FROM tarifasHonorariosAsp
                    WHERE documento = ?
                    AND entidad = ?
                )
                SELECT DISTINCT
                    pc.nombre AS procedipro
                FROM procedimientos_cup pc
                INNER JOIN procedimientos_profesional pp
                    ON pp.procedipro = pc.nombre
                ORDER BY pc.nombre";

            $bindings = [$cupCode, $cedula,$entidad];
            return DB::select($query, $bindings);

        } catch (\Throwable $e) {
            throw new ServerErrorException(
                'Error consultando procedimientos: ' . $e->getMessage(),
                500
            );
        }
    }

    private function mapCalendar($unMappedCalendar){
        $calendarMapper=new CalendarProfesionalMapper();
       return $calendarMapper->map($unMappedCalendar);

    }
    public function getScheduleByCedula(array $data): array
    {
        $cedula = $data['cedula'];
        $query = "SELECT s.id,
                    s.cedula,
                    s.sede as cod_sede,
                    se.nombre AS sede,
                    s.dia_semana,
                    s.fecha_inicio ,
                    s.fecha_fin FROM 
                    profesional_calendarios s INNER JOIN sede se ON se.cod = s.sede
                    WHERE s.cedula = ?";
        $schedule= DB::select(query:$query,bindings:[$cedula]);
        return $this->responseManager->success($schedule);
    }
    public function findSchedulePorfesionalByDays(string $cedula, array $weekDays ){

        $placeholders= implode(', ', array_fill(0, count($weekDays), '?'));
        $query="SELECT s.id,
                    s.cedula,
                    s.sede as cod_sede,
                    CONCAT(RTRIM(se.direccion), ' BARRIO ', RTRIM(se.barrio) )AS direccion,
                    s.dia_semana,
                    s.fecha_inicio ,
                    s.fecha_fin FROM 
                    profesional_calendarios s INNER JOIN sede se ON se.cod = s.sede
            WHERE s.cedula = ?
            AND s.dia_semana  IN ($placeholders)";

        $schedule =  DB::select(query:$query,bindings: [$cedula, ...$weekDays]);
        if (empty($schedule)){
            throw new BadRequestException("El profesional no tiene disponibilidad ningun día de los seleccionados",404);
        }
        return collect($schedule)->mapInto(ScheduleModel::class)->toArray();
    }
    
    



    
}