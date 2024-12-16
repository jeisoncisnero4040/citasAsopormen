<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\ErrorSavingCitas;
use App\Mappers\CalendarClientMapper;
use App\Models\CitasModel;
use App\Requests\citasRequests;
use App\utils\DateManager;
use App\utils\ResponseManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mappers\CalendarProfesionalMapper;
use App\utils\CelNumberManager;

class CitasService{
    private $citasModel;
    private $responseManager;
    private $whatsappService;
    private $observaCitasService;

    public function __construct(CitasModel $citasModel, ResponseManager $responseManager,
                                WhatsappService $whatsappService, ObservaCitasService $observaCitasService)
    {
        $this->responseManager=$responseManager;
        $this->citasModel=$citasModel;
        $this->whatsappService=$whatsappService;
        $this->observaCitasService=$observaCitasService;

    }

    public function createGroupCitas($request){
        
        $this->validateCitas($request);

        $schedule=$this->getSchedule($request);
        $citaInDto=$this->getCitasInDto($request);
        $dataNumSessions=$this->getDataNumSessions($request);

        $startDate =Carbon::parse($schedule['start_date'])->setTimezone('America/Bogota')->addHours(5);
        $weekDays=$schedule['week_days'];
        $numSessions=$schedule['num_sessions'];
        $numCitas=$schedule['num_citas'];
        $sessionDuration=$schedule['duration_session'];

        $this->checkStartDateLaterToday($startDate->copy());
        $this->checkStartDateInDaysWeek($startDate,$weekDays);
        $this->checkLimitSessionsToSave($dataNumSessions);
        $this->checkRememberWhatsWhitObservations($citaInDto);


        $diferenceBeetwenDays=$this->getDifBeetwenDays($weekDays);
        $scheduleCitas=$this->CreateSchedule($numSessions,$numCitas,$sessionDuration,$startDate,$weekDays,$diferenceBeetwenDays);
        $citasIds=$this->saveCitas($scheduleCitas,$citaInDto);
        if($this->CheckReassingCitasRequests($request)){
            $idCitasCanceledRegister=$request['id'];
            $this->saveNewsIdsInCitaCanceledRegister($idCitasCanceledRegister,$citasIds);
        }
        
        if($this->checkCitasHasRememberMesssage($citaInDto)){
             
            $dataToSendMessage=$this->makeDataToSendMsm($scheduleCitas,$citaInDto,$weekDays);
            $this->whatsappService->sendNotificationOrdenProgramed($dataToSendMessage);
        }
        if($this->citasIsRememberebleAndFirstCitasIsNear($citaInDto,$scheduleCitas[0])){
            $dataToSendCitaToRemember=$this->CreateDataToRemeberCita($citasIds,$scheduleCitas,$sessionDuration,$citaInDto);
            $this->whatsappService->rememberFisrtCita($dataToSendCitaToRemember);
        }

        
        return $this->responseManager->success(count($citasIds),200);
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

        $citasFiltered=$this->filterCitasAvaibles($calendarMapped);
        $CitasByProcedureAndDate=$this->countCitasByTime($citasFiltered);
        $response=['calendar'=>$calendarMapped,
                    'schedule'=>$CitasByProcedureAndDate
        ];
        return $this->responseManager->success($response);

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
        if(empty($cita)){
            throw new NotFoundException("la cita actaul no fue encontrada",404);
        }

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
    public function corfirmateGroupSessions($request){

        citasRequests::ValidateCitaSessionsIds($request,'confirmar');
        $DateConfirmation=Carbon::now()->format('Y-m-d H:i:s');
        $sessionConfirmate=$this->sendQueryToConfirmGroupSessions($request,$DateConfirmation);
        if (!$sessionConfirmate) {
            throw new BadRequestException("No es posible cancelar esta cita", 400);
        }
        return $this->responseManager->success($sessionConfirmate);
        
    }
    public function getAllCitasCanceled(){
        $citasCanceled=$this->sendQueryByGetAllCitasCanceled();
        if(empty($citasCanceled)){
            throw new NotFoundException("no hay citas canceladas",404);

        }
        return $this->responseManager->success($citasCanceled);
    }
    public function CancelGroupSsessions($request){
        citasRequests::ValidateCitaSessionsIds($request,"cancelar");
        $citasCanceled=$this->sendQueryToCancelGroupSessions($request);
        return $this->responseManager->success($citasCanceled);
    }

    public function unactivateCitaCanceledById($request) {
        if (empty($request['id'])) {
            throw new BadRequestException("Cita's ID is required", 400);
        }
        $idCitaCanceled = $request['id'];
        $citaUnactivate = $this->sendQueryToUnactivateCitaCanceled($idCitaCanceled);

        if($citaUnactivate==0){
            throw new NotFoundException("cita canceled not found", 404);
        }

        return $this->responseManager->success($citaUnactivate);
    }
    public function ChangeProfesionalToCitaIdsGroup($request){
        citasRequests::validateDataToChangeProfesional($request);
        $citasChanged=$this->sendQueryToChangeProfesionalsCitas($request);
        return $this->responseManager->success($citasChanged);
        
    }
    public function getCitasClient($clientCode){
        if(empty(trim($clientCode))){
            throw new BadRequestException("El código de clente debe ser valido",400);
        }
        $citas=$this->sendQueryToGetCitasClient($clientCode);
        $calendarMapped=$this->mapCalendarClient($citas);
        $citasGroupedBySessions=$this->GroupCitasBysessions($calendarMapped);

        return $this->responseManager->success(($citasGroupedBySessions));
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
    private function CheckReassingCitasRequests($request){
        return CitasRequests::checkIsReassingCitas($request);
    }
    private function makeDataToSendMsm($scheduleCitas,$citaInDto,$weekDays){
        $laterDay = $scheduleCitas[count($scheduleCitas) - 1];
        $telephoneNumberClean=CelNumberManager::chooseTelephoneNumber($citaInDto['client_number_cel']);

        $dataToSendMessage=[
            'client'=>$citaInDto['clientName'],
            'week_days'=>$weekDays,
            'first_day'=>$scheduleCitas[0],
            'laterDay'=>$laterDay,
            'telephone_number'=>$telephoneNumberClean

        ];
        return $dataToSendMessage;
    }
    private function getSchedule(array $request) {
        $keysToExtract = ['start_date', 'week_days', 'num_sessions', 'num_citas', 'duration_session'];
        $selectedFields = array_intersect_key($request, array_flip($keysToExtract));
        return $selectedFields;
    }
    private function getCitasInDto(array $request){
        $keysToExtract = ["nro_hist", "cedprof", "ced_usu", "registro", 
        "sede", "regobserva", "codent", "codent2", "procedipro",
        "n_autoriza","procedim","tiempo","direccion_cita","recordatorio_wsp",
        "notication_orden_programed","copago","client_number_cel","profesional","clientName"];
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
  
    private function checkStartDateLaterToday(Carbon $startDate) {
        $now = Carbon::now('America/Bogota');
        if ($startDate->isBefore($now->startOfDay())) {
            throw new BadRequestException("La fecha de inicio solo puede ser después de hoy.", 400);
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
    
        return $citas; 
    }
    
    
    private function citaInDtoToCita($session, $citaInDto){
        $sessionCopy=$session->copy();
        $citaComplement = [
            'hora' => DateManager::getHoursOfDateInAmPmFormat($sessionCopy),
            'fecha' => $sessionCopy->setTime(hour: 0, minute: 0)->format('Y-m-d H:i:s'),
            'fec_hora' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    
        return array_merge($citaInDto, $citaComplement);
         
    }

    public function saveCitasInBd($cita, $cont = 0) {
        try {
            $cita = DB::insert("
                INSERT INTO citas (
                    nro_hist, cedprof, ced_usu, registro,sede,observaciones_mc, codent, codent2,tiempo,direccion_cita,procedim, procedipro,autoriz, fecha, hora, fec_hora, recordatorio_wsp,copago
                )
                VALUES (?,?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?,  
                    CONVERT(smalldatetime, ?, 120), 
                    ?, 
                    CONVERT(smalldatetime, ?, 120), 
                    ?,?
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
                $cita['recordatorio_wsp'],
                $cita['copago']
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
    private function checkCitasHasRememberMesssage($citaInDto){
        return $citaInDto['notication_orden_programed'];
    }
    private function citasIsRememberebleAndFirstCitasIsNear($citaInDto, $firstCitaDate) {
        $rememberWhatsapp = isset($citaInDto['recordatorio_wsp']) && $citaInDto['recordatorio_wsp'];
        $citaIsNear = $firstCitaDate->diffInDays(Carbon::now()) <= 2;
        return $rememberWhatsapp && $citaIsNear;
    }
    private function CreateDataToRemeberCita($citasIds, $scheduleCita, $sessionDuration, $citaInDto) {
        $sessionIds = $this->getidsSessionToRemember($citasIds, $scheduleCita, $sessionDuration);
        $profesional=$citaInDto['profesional'];
        $client=$citaInDto['clientName'];
        $direction=$citaInDto['direccion_cita'];
        $procedim=$citaInDto['procedim'];
        $date=$scheduleCita[0]->format('Y-m-d H:i');
        $telephoneNumber=CelNumberManager::chooseTelephoneNumber($citaInDto['client_number_cel']);
        $observation=$this->getObservation($citaInDto);
        return [
            'client'=>$client,
            'profesional'=>$profesional,
            'telephone_number' => $telephoneNumber,
            'date'=>$date,
            'procedim'=>$procedim,
            'direction'=>str_replace('BARRIO','barrio',$direction),
            'session_ids'=>$sessionIds,
            'observations'=>$observation
        ];

        
    }
    
    private function getidsSessionToRemember($citasIds, $scheduleCita, $sessionDuration) {
        $sessionIds = $citasIds[0]; 
        error_log($scheduleCita[0]);
        $numCitas = count($citasIds)-1;
    
        for ($index = 1; $index < $numCitas; $index++) {
            $previousTime = $scheduleCita[$index - 1]->copy()->addMinutes($sessionDuration);
            
            if ($previousTime->equalTo($scheduleCita[$index])) {
                $sessionIds .= '|||' . $citasIds[$index]; 
            } else {
                break; 
            }
        }
    
        return $sessionIds; 
    }
    private function getObservation($citaInDto){
        $copago=$citaInDto['copago'];
        $idObservation=$citaInDto['regobserva'];
        $observaCitasResponse=$this->observaCitasService->getObservationContentById((integer)$idObservation);
        if ($observaCitasResponse['status']!==200){
            throw new ServerErrorException("el paquete de citas fué creado, pero no se pudo enviar la notificacion por whatsapp
                                            CAUSA: no se encontro la plantilla de observacion",500);
        }
        $observationTemplate=$observaCitasResponse['data']->contenido;
        $observation=str_replace('{{}}',$copago,$observationTemplate);
        return $observation;


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
                ci.autoriz AS autorizacion,
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
            $rowsDeleted=DB::delete("DELETE FROM citas WHERE id = ? AND cancelada = '0' AND asistio = '0' AND na = '0'
                                    AND autoriz !=''", [$id]);
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
                (na = 0) AND
                (autoriz != '')
                ",[$day,$cedulaPro]
            );
            return ['citas_eliminadas'=>$numCitasDeletes];
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function sendQueryByGetCitaById($id) {
        try {
           $cita=DB::select("
                        SELECT 
                        ci.id,
                        ci.fecha,
                        ci.hora AS hora,
                        ci.fec_hora AS hora_asignacion,
                        ci.observaciones_mc AS procedimiento,
                        ci.asistio AS asistida,
                        ci.cancelada AS cancelada,
                        ci.na AS no_asistida,
                        ci.tiempo AS orden,
                        ci.copago,
                        pro.duraccion AS duracion,
                        ci.direccion_cita AS direccion,
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
                        ci.id = ?
           ",[$id]);
            return $cita;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function sendQueryToGetCalendarProfesional($cedula,$startDate,$endDate) {
        try{
           $calendar=DB::select(
               "
                 SELECT 
                    ci.id,
                    ci.fecha,
                    ci.hora AS hora,
                    ci.autoriz AS autorizacion,
                    ci.tiempo AS tiempo,
                    ci.procedipro,
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
      private function filterCitasAvaibles($calendar) {
        $citasAvaibles = [];
        foreach ($calendar as $cita) {
            
            if ($cita->asistida == '0' && $cita->no_asistida == '0' && $cita->cancelada == '0') {
                $citasAvaibles[] = $cita;
            }
        }
        return $citasAvaibles;
    }
    
    private function countCitasByTime($citas) {
        $citasByDate = [];
        foreach ($citas as $cita) {
             
            $title = "$cita->start ** $cita->end ** $cita->procedipro";
            
             
            if (isset($citasByDate[$title])) {
                $citasByDate[$title] += 1;
            } else {
                $citasByDate[$title] = 1;
            }
        }
    
        $citasCounted = [];
        foreach ($citasByDate as $key => $value) {
            
            $dataCita = explode(' ** ', $key);
    
             
            if (count($dataCita) === 3) {
                $start = $dataCita[0];
                $end = $dataCita[1];
                $procedimiento = $dataCita[2];
                $numCitas = $value;  
    
                 
                $citasCounted[] = [
                    'start' => $start,
                    'end' => $end,
                    'title' => "$procedimiento",
                    'color'=>$numCitas==1?'#FF5733':($numCitas==2?'#33FF57':'#1b0dd3')
                ];
            }
        }
    
        return $citasCounted;
    }
    private function checkObservations($observations){
        $arrayWords=explode(" ",$observations);
        count($arrayWords)<=3? throw new BadRequestException('La observacion no es valida',400):null;

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
    private function sendQueryToConfirmGroupSessions($request,$date) {
        $idsListInString =(string)$request['ids'];  
        $idsArray = explode('|||', $idsListInString);  
        $idsForQuery = array_map('intval', $idsArray);  
    
        try {
             
            $placeholders = implode(',', array_fill(0, count($idsForQuery), '?'));
    
            $cita = DB::update(
                "
                UPDATE citas SET confirma = '1', 
                fconfir = CONVERT(smalldatetime, ?, 120) 
                WHERE id IN ($placeholders) 
                AND asistio = '0' 
                AND na = '0'",
                array_merge([$date], $idsForQuery)
            );
            return $cita;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function sendQueryByGetAllCitasCanceled(){
        try{
            $citas=DB::select(
                "
                    SELECT 
                        cica.id,
                        cica.num_sessions_canceled AS cantidad,
                        cica.num_sessions_reassing AS reasignadas, 
                        cica.ids_sessions AS ids,
                        cica.date_cita_canceled AS fecha, 
                        ci.direccion_cita,
                        ci.nro_hist,
                        ci.codent,
                        ci.codent2,
                        ci.autoriz,
                        ci.procedim,
                        ci.tiempo,
                        ci.procedipro AS procedimiento,
                        ci.realizar AS razon,
                        ci.sede AS cod_sede,
						ci.copago,
                        pro.recordatorio_whatsapp,
                        pro.duraccion AS duracion,
                        se.nombre AS sede,
						em.enombre AS profesional,
						cli.nombre AS cliente,
                        cli.cel AS celular,
						oc.nombre AS nombre_procedimiento
                    FROM citas_canceladas cica
                    INNER JOIN citas ci ON ci.id = cica.id_example
                    INNER JOIN procedipro pro ON pro.nombre=ci.procedipro
                    INNER JOIN sede se ON se.cod=ci.sede
                    INNER JOIN emplea em ON em.ecc = ci.cedprof
                    INNER JOIN cliente cli ON cli.codigo =ci.nro_hist
					INNER JOIN observa_citas oc ON oc.id=CAST(ci.regobserva AS INT)
                    WHERE cica.num_sessions_canceled > cica.num_sessions_reassing
                    AND cica.activa='1'
    
                "
            );
            return $citas;

        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function sendQueryToCancelGroupSessions($request){
        $dateCita = $request['fecha_cita'];
        $dateCancelation = Carbon::now()->format('Y-m-d H:i:s');
        $idsToCancel = $request['ids'];
        $idExample = $this->getIdExample($idsToCancel);
        $razon = $request['razon'];
        $numSessionsCanceled = count(explode('|||', $idsToCancel));
    
        $idsArray = explode('|||', $idsToCancel);  
        $idsForQuery = array_map('intval', $idsArray);  
        $placeholders = implode(',', array_fill(0, count($idsForQuery), '?'));
    
        try {
            $citasCanceled = DB::transaction(function () use ($razon, $dateCancelation, $placeholders, $idsForQuery, $idsToCancel, $idExample, $numSessionsCanceled, $dateCita) {
                
                $citasCanceled = DB::update(
                    "
                    UPDATE citas 
                    SET na = '1', 
                        realizar = ?, 
                        fec_can = CONVERT(smalldatetime, ?, 120) 
                    WHERE id IN ($placeholders) 
                    AND asistio = '0' 
                    AND na = '0'",
                    array_merge([$razon, $dateCancelation], $idsForQuery)
                );
        
                
                if ($citasCanceled > 0) {
                    DB::insert(
                        "
                        INSERT INTO citas_canceladas 
                        (ids_sessions, id_example, num_sessions_canceled, date_cita_canceled)
                        VALUES (?, ?, ?, CONVERT(smalldatetime, ?, 120))",
                        [$idsToCancel, $idExample, $numSessionsCanceled, $dateCita]
                    );
                }
        
                
                return $citasCanceled;
            });
        
            return $citasCanceled;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    private function getIdExample($ids){
        return explode('|||', $ids)[0];
    }
    private function saveNewsIdsInCitaCanceledRegister($idCitaCanceled, $ids) {
        $idsToString = join('|||', $ids);
    
        try {
            DB::update("
            UPDATE citas_Canceladas
            SET new_session_ids = CONCAT(
                COALESCE(new_session_ids, ''), '', ?
            ),
            num_sessions_reassing = num_sessions_reassing + ?
            WHERE id = ?
        ", [$idsToString, count($ids), $idCitaCanceled]);
        } catch (\Exception $e) {
            return null;
        }
    }
    private function sendQueryToUnactivateCitaCanceled($idCitaCanceled) {
        try {
            $citasUnactivates = DB::update("
                UPDATE citas_canceladas
                SET activa = '0'
                WHERE id = ?
            ", [$idCitaCanceled]);
            
            return $citasUnactivates;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function sendQueryToChangeProfesionalsCitas($request) {
        $ids = $request['ids'];
        $newProfesionalIdentity = $request['cedprof'];
    
        $idsForQuery = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($idsForQuery), '?'));
    
        try {
            $citasChangedProfesional = DB::update("
                UPDATE citas
                SET cedprof = ?
                WHERE id IN ($placeholders)
            ", array_merge([$newProfesionalIdentity], $idsForQuery));
            
            return $citasChangedProfesional;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function sendQueryToGetCitasClient(string $ClientCod){
        try{
            $citas=DB::select(
                "
                SELECT 
                    ci.id,
                    ci.fecha,
                    ci.hora AS hora,
                    ci.autoriz AS autorizacion,
                    ci.procedim AS procedimiento,
                    ci.regobserva AS observaciones,
                    ci.asistio AS asistida,
                    ci.cancelada AS cancelada,
                    ci.tiempo,
                    ci.direccion_cita as direcion,
                    ci.na as no_asistida,
                    pro.duraccion AS duracion,
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
                    AND ci.fecha >= GETDATE()

                ORDER BY 
                    ci.fecha ASC;
                ",[$ClientCod]);
            return $citas;
         }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
         }
    }
    private function GroupCitasBysessions($citas){
        $citasMapper=new CalendarClientMapper();
        return $citasMapper->groupCitasBySessions($citas);
    }


}