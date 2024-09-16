<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\ErrorSavingCitas;
use App\Models\CitasModel;
use App\Requests\citasRequests;
use App\utils\DateManager;
use App\utils\ResponseManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mappers\CalendarProfesionalMapper;


class CitasService{
    private $citasModel;
    private $responseManager;

    public function __construct(CitasModel $citasModel, ResponseManager $responseManager)
    {
        $this->responseManager=$responseManager;
        $this->citasModel=$citasModel;

    }

    public function createGroupCitas($request){
        

        $schedule=$this->getSchedule($request);
        $citaInDto=$this->getCitasInDto($request);
        $dataNumSessions=$this->getDataNumSessions($request);

        $startDate =Carbon::parse($schedule['start_date'])->setTimezone('America/Bogota')->addHours(5);
        $weekDays=$schedule['week_days'];
        $numSessions=$schedule['num_sessions'];
        $numCitas=$schedule['num_citas'];
        $sessionDuration=$schedule['duration_session'];

        $this->checkStartDateInFuture($startDate->copy());
        $this->validateCitas($request);
        $this->checkStartDateInDaysWeek($startDate,$weekDays);
        $this->checkLimitSessionsToSave($dataNumSessions);
        $this->checkRememberWhatsWhitObservations($citaInDto);


        $diferenceBeetwenDays=$this->getDifBeetwenDays($weekDays);
        $scheduleCitas=$this->CreateSchedule($numSessions,$numCitas,$sessionDuration,$startDate,$weekDays,$diferenceBeetwenDays);
        $numSessionsSaved=$this->saveCitas($scheduleCitas,$citaInDto);
        return $this->responseManager->success($numSessionsSaved,200);
    }

    public function getNumCitasFromOrder($authorization, $codProcedim)
    {
        $this->validateAuthorizationAndProcedim($authorization, $codProcedim);
        $numCitasFromOrder = $this->citasModel::where('autoriz', $authorization)
                                ->where('tiempo', $codProcedim)
                                ->where('cancelada','!=', '1')
                                ->count();
    
        return $this->responseManager->success($numCitasFromOrder);
    }

    public function getCitasByClientInRangeTime($request){
        citasRequests::ValidateTineRangeFromCitasClient($request);
        $codigo=$request['codigo'];
        $startDate=Carbon::parse($request['startDate']);
        $endDate=Carbon::parse($request['endDate']);
        
        $this->checkEndDateUpStartDate($endDate,$startDate);

        $startDate=DateManager::getDateInSmallDateTime($startDate);
        $endDate=DateManager::getDateInSmallDateTime($endDate);

        $calendarClient=$this->sendQueryToGetCalendarClient($codigo,$startDate,$endDate);
        $calendarMapped=$this->mapCalendarClient($calendarClient);

        return $this->responseManager->success($calendarMapped);

    }
    public function getCitasByProfesionalInRangeTime($request){
        citasRequests::ValidateTineRangeFromCitasProfesional($request);
        $cedula=$request['cedula'];
        $startDate=Carbon::parse($request['startDate']);
        $endDate=Carbon::parse($request['endDate']);
        
        $this->checkEndDateUpStartDate($endDate,$startDate);

        $startDate=DateManager::getDateInSmallDateTime($startDate);
        $endDate=DateManager::getDateInSmallDateTime($endDate);

        $calendarClient=$this->sendQueryToGetCalendarProfesional($cedula,$startDate,$endDate);
        $calendarMapped=$this->mapCalendarClient($calendarClient);
        return $this->responseManager->success($calendarMapped);

    }
    public function deleteCitaById($id){
        $citasDeleted=$this->sendQuerydeleteCitaById($id);
        if ($citasDeleted==0){
            throw new BadRequestException("no es posible eliminar esta sección",400);
        }
        return $this->responseManager->delete('cita con id '.$id);
    }

    public function deleteDayCitasProfesional($request){
        citasRequests::vaalidateDateAndCedulaProfesional($request);
        $cedulaPro=$request['profesional_identity'];
        $day=Carbon::parse($request['day']);
        DateManager::getDateInSmallDateTime($day);
        $deletedCitas=$this->sendQueryDeleteAllCitasDay($day,$cedulaPro);
        return $this->responseManager->success($deletedCitas);
    }

    public function getCitasById($id){
        $cita=$this->sendQueryByGetCitaById($id);
        return $this->responseManager->success($cita);
    }
    public function cancelCita($request){
        citasRequests::ValidateRealizarField($request);
        $id=$request['id'];
        $observations=$request['realizar'];
        $this->checkObservations($observations);
        $citacancel=$this->sendQueryToCancelCita($id,$observations);
        return $this->responseManager->success($citacancel);


    }

    private function validateCitas($request){
        citasRequests::validateCitasClient($request);
        citasRequests::validateCitasUser($request);
        citasRequests::validateCitasProfesional($request);
        citasRequests::validateCitasCentral($request);
        citasRequests::validateCitasProcedure($request);
        citasRequests::validateCitasSchedule($request);
        citasRequests::validateCitasAuthorization($request);
    }
    private function getSchedule(array $request) {
        $keysToExtract = ['start_date', 'week_days', 'num_sessions', 'num_citas', 'duration_session'];
        $selectedFields = array_intersect_key($request, array_flip($keysToExtract));
        return $selectedFields;
    }
    private function getCitasInDto(array $request){
        $keysToExtract = ["nro_hist", "cedprof", "ced_usu", "registro", 
        "sede", "regobserva", "codent", "codent2", "procedipro",
        "n_autoriza","procedim","tiempo","direccion_cita","recordatorio_wsp"];
        $selectedFields = array_intersect_key($request, array_flip($keysToExtract));
        return $selectedFields;
    }
    private function getDataNumSessions(array $request){
        $keysToExtract = ["all_sessions","saved_sessions","num_sessions","num_citas"];
        $selectedFields = array_intersect_key($request, array_flip($keysToExtract));
        return $selectedFields;
    }

    private function checkStartDateInDaysWeek($start_date, $week_days){
        $nameDayStart=DateManager::getDayByDate($start_date);
        if (!in_array($nameDayStart, $week_days)) {
            throw new BadRequestException("El dia de inicio no coincide con los dias agregados al horario de citas", 400);
        }
    }
  
    private function checkStartDateInFuture(Carbon $startDate) {
         
        $now = Carbon::now('America/Bogota')->subHours(1);
        
        if ($startDate->isBefore($now)) {
            throw new BadRequestException("La fecha de inicio no puede ser anterior a la hora actual por mas de una hora.", 400);
        }
    }
    
    
    
    private function checkLimitSessionsToSave($dataSesions){
        $allSessions=$dataSesions['all_sessions'];
        $saved_sessions=$dataSesions['saved_sessions'];
        $num_sessions=$dataSesions['num_sessions'];
        $num_citas=$dataSesions['num_citas'];

        $sessionsToSave=$num_sessions*$num_citas;
        $sessionsAvaibles=$allSessions-$saved_sessions;
        if ($sessionsToSave > $sessionsAvaibles){
            throw new BadRequestException("el numero de secciones seleccionadas excede la cantidad disponible ",400);
        }
    }
    private function checkRememberWhatsWhitObservations($citaInDto){
        $rememberWhats=$citaInDto['recordatorio_wsp'];
        $observations=$citaInDto['regobserva'];

        if($rememberWhats && empty(trim($observations))){
            throw new BadRequestException("Es necesario observaciones para guardar citas con recordatorio
                                            en whatsapp
                                            ",400);
        }

    }
    
    private function getDifBeetwenDays($weekDays){
        $daysDictionary=DateManager::$daysWeekInverted;
        $diferenceDays=[];
        $sizeWeekDays=count($weekDays);

        for ($indexDay=0;$indexDay<$sizeWeekDays;$indexDay++){
            try{
                $diference = $daysDictionary[$weekDays[$indexDay + 1]] - $daysDictionary[$weekDays[$indexDay]];
                $diferenceDays[] = $diference;
            }catch(\Exception $e){
                $leftDays = 7- $daysDictionary[$weekDays[$sizeWeekDays - 1]];
                $daysLaterStartWeek = $daysDictionary[$weekDays[0]];
                $diferenceDays[] = $leftDays + $daysLaterStartWeek;
            }
        }
        return $diferenceDays;
    }
    private function CreateSchedule($numSessions, $numCitas, $sessionDuration, $startDate, $weekDays, $diferenceDays) {
        $nameDayStart = DateManager::getDayByDate($startDate);
        $startIndex = array_search($nameDayStart, $weekDays);

        $hour = DateManager::getHourOfDate($startDate);
        $minute = DateManager::getMinuteOfDate($startDate);
    
        $schedule = [];
        $indexDay = $startIndex;
        
        while ($numCitas > 0) {
            
            $startHour = $startDate->setTime(hour:$hour, minute:$minute);
             
            if (!DateManager::isHoliday($startHour)) {
                for ($session = 0; $session < $numSessions; $session++) {
                    $schedule[] = $startDate->copy();  
                    $startDate->addMinutes($sessionDuration);
                }
                $numCitas -= 1;
            }
    
             
            $startDate->addDays($diferenceDays[$indexDay]);
            $indexDay = ($indexDay + 1) % count($diferenceDays);
            
        }
    
        return $schedule;
    }
    
    private function saveCitas($schedule, $citaInDto){
        $citas = [];

    
        foreach ($schedule as $session) {
            $cita = $this->citaInDtoToCita($session, $citaInDto);
    
            try {
                $citaSaved = $this->saveCitasInBd($cita);
                $citas[] = $citaSaved;

            } catch (ErrorSavingCitas $e) {
                
                if (!empty($ids)) {
                    $idsString = implode(' AND id = ', $citas); 
                    DB::delete("DELETE FROM citas WHERE id = {$idsString}");
                }
                throw new ServerErrorException("No fue posible guardar el paquete de citas", 500);
            }
        }
    
        return count($citas); 
    }
    
    
    private function citaInDtoToCita($session, $citaInDto){
        $citaComplement = [
            'hora' => DateManager::getHoursOfDateInAmPmFormat($session),
            'fecha' => $session->setTime(hour: 0, minute: 0)->format('Y-m-d H:i:s'),
            'fec_hora' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    
        return array_merge($citaInDto, $citaComplement);
         
    }

    public function saveCitasInBd($cita, $cont = 0) {
        try {
            $cita = DB::insert("
                INSERT INTO citas (
                    nro_hist, cedprof, ced_usu, registro,sede, regobserva, codent, codent2,tiempo,direccion_cita,procedim, procedipro,autoriz, fecha, hora, fec_hora, recordatorio_wsp
                )
                VALUES (?,?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?,  
                    CONVERT(smalldatetime, ?, 120), 
                    ?, 
                    CONVERT(smalldatetime, ?, 120), 
                    ?
                )
            ", [
                $cita['nro_hist'],   
                $cita['cedprof'],   
                $cita['ced_usu'],      
                $cita['registro'],
                $cita['sede'],           
                $cita['regobserva'],
                $cita['codent'],           
                $cita['codent2'],
                $cita['tiempo'],  
                $cita['direccion_cita'],    
                $cita["procedim"],
                $cita['procedipro'],
                $cita["n_autoriza" ],        
                $cita['fecha'],                     
                $cita['hora'],                      
                $cita['fec_hora'],                   
                $cita['recordatorio_wsp']
            ]);
            $id= DB::getPdo()->lastInsertId();
            return $id;
        } catch (\Exception $e) {
            if ($cont >= 4) {
                throw new ErrorSavingCitas();
            }
            return $this->saveCitasInBd($cita, $cont + 1);
        }
    }
    

    private function validateAuthorizationAndProcedim($authorization,$codProcedim){
        if (empty(trim($authorization))){
            throw new BadRequestException("el numero de autorizacion debe ser valido",400);
        }
        if (empty(trim($codProcedim))){
            throw new BadRequestException("el codigo de procedimiento debe ser valido",400);
        }
    }
    private function checkEndDateUpStartDate(Carbon $endDate, Carbon $startDate){
        if($endDate->isBefore($startDate)){
            throw new BadRequestException("la fecha de final de periodo debe ser despues de la fecha de inicio",400);
        }
    }
   private function sendQueryToGetCalendarClient($codigo,$startDate,$endDate) {
     try{
        $calendar=DB::select(
            "
            SELECT 
                ci.id,
                ci.fecha,
                ci.hora AS hora,
                ci.procedim AS procedimiento,
                ci.regobserva AS observaciones,
                ci.asistio AS asistida,
                ci.cancelada AS cancelada,
                ci.tiempo,
                ci.na as no_asistida,
                pro.duraccion AS duracion,
                cli.nombre AS usuario,
                em.enombre AS profesional
            FROM 
                citas ci
            INNER JOIN 
                cliente cli ON cli.codigo LIKE '%' +ci.nro_hist  + '%'
            INNER JOIN 
                procedipro pro ON pro.nombre = ci.procedipro
            INNER JOIN 
                emplea em ON em.ecc = ci.cedprof
            WHERE 
                ci.nro_hist = ?
                AND ci.fecha BETWEEN 
                    CONVERT(smalldatetime,?, 120) 
                    AND CONVERT(smalldatetime,?, 120)
            ORDER BY 
                ci.hora ASC;
            ",[$codigo,$startDate,$endDate]);
        if (empty($calendar)){
            throw new NotFoundException("El cliente no registra citas en este periodo de tiempo",404);
        }
        return $calendar;
     }catch(\Exception $e){
        throw new ServerErrorException($e->getMessage(),500);
     }
   }
    private function mapCalendarClient($unMappedCalendar){
        $calendarMapper=new CalendarProfesionalMapper();
        return $calendarMapper->map($unMappedCalendar);

    }

    private function sendQuerydeleteCitaById($id){
        try{
            $rowsDeleted=DB::delete("DELETE FROM citas WHERE id = ? AND cancelada = '0' AND asistio = '0' AND na = '0'", [$id]);
            return $rowsDeleted;
        }catch(\Exception  $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }

    private function sendQueryDeleteAllCitasDay($day,$cedulaPro){
        try{
            $numCitasDeletes=DB::delete(
                "
                DELETE CITAS WHERE fecha = 
                CONVERT(smalldatetime,?, 120) AND
                (cedprof = ?) AND
                (cancelada=0) AND
                (asistio=0) AND
                (na = 0)
                ",[$day,$cedulaPro]
            );
            return ['citas_eliminadas'=>$numCitasDeletes];
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function sendQueryByGetCitaById($id){
        $cita=DB::select("
            SELECT 
            ci.id,
            ci.fecha,
            ci.hora AS hora,
            ci.fec_hora as hora_asignacion,
            ci.procedim AS procedimiento,
            ci.regobserva AS observaciones,
            ci.asistio AS asistida,
            ci.cancelada AS cancelada,
            ci.na as no_asistida,
            ci.tiempo as orden,
            ci.regobserva as observaciones,
            pro.duraccion AS duracion,
            ci.direccion_cita as direcion,
            cli.nombre AS usuario,
            em.enombre AS profesional
        FROM 
            citas ci
        INNER JOIN 
            cliente cli ON cli.codigo LIKE '%' +ci.nro_hist  + '%'
        INNER JOIN 
            procedipro pro ON pro.nombre = ci.procedipro
        INNER JOIN 
            emplea em ON em.ecc = ci.cedprof
        WHERE 
            ci.id =?

        ",[$id]);
    
        if(empty($cita)){
            throw new NotFoundException("cita no encontrada",404);
        }
        return $cita;
    }
    private function sendQueryToGetCalendarProfesional($cedula,$startDate,$endDate) {
        try{
           $calendar=DB::select(
               "
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
                   AND ci.fecha BETWEEN 
                       CONVERT(smalldatetime,?, 120) 
                       AND CONVERT(smalldatetime,?, 120)
               ORDER BY 
                   ci.hora ASC;
               ",[$cedula,$startDate,$endDate]);
           if (empty($calendar)){
               throw new NotFoundException("El profesional no registra citas en este periodo de tiempo",404);
           }
           return $calendar;
        }catch(\Exception $e){
           throw new ServerErrorException($e->getMessage(),500);
        }
      }
    private function checkObservations($observations){
        $arrayWords=explode(" ",$observations);
        count($arrayWords)<=5? throw new BadRequestException('La observacion no es valida',400):null;

    }
    private function sendQueryToCancelCita($id,$observations){
        try{
            $cita = DB::update("UPDATE citas SET cancelada = '1', realizar = ? WHERE id = ? AND asistio = '0' AND na = '0'" , [$observations,$id]);
            if(!$cita){
                throw new BadRequestException("no es posible cancelar esta cita",400);
            }
            return null;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
}