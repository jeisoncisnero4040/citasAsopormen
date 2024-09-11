<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mappers\CalendarProfesionalMapper;
use Illuminate\Support\Facades\DB;
use App\utils\ResponseManager;
use Exception;

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

        $profesionals = DB::select("
            SELECT TOP 20 emp.ecc, emp.enombre, esp.nombre
            FROM emplea emp
            INNER JOIN especial esp ON emp.especialidad = esp.cod
            WHERE emp.agenda = 1 AND UPPER(emp.enombre) LIKE ?
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
    private function mapCalendar($unMappedCalendar){
        $calendarMapper=new CalendarProfesionalMapper();
       return $calendarMapper->map($unMappedCalendar);

    }
    
}