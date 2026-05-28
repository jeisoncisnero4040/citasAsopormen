<?php

namespace App\Services;

use App\Dtos\CreateCitasDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mappers\CalendarClientMapper;
use App\Models\CitasModel;
use App\Requests\CitasRequests;
use App\utils\DateManager;
use App\utils\ResponseManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mappers\CalendarProfesionalMapper;
use App\Mappers\AppoimentsMapper;
use App\Domain\CitasDomain;
use App\Dtos\CloneCalendarDto;
use App\Dtos\DeleteAppoDto;
use App\Kafka\Domain\MessageQueue;
use App\Models\AuditMessageQueueBuilder;
use App\Models\UserRequesting;
use App\Repositories\CitasRepository;
use Illuminate\Support\Str;
use App\Services\QueueService;
use stdClass;

class CitasService{
    private $citasModel;
    private $responseManager;
    private CitasRepository $citasRepository;
    private ProcedureService $procediproService;
    private ProfesionalService $profesionalService;
    private QueueService $queueService;

    public function __construct(CitasModel $citasModel, ResponseManager $responseManager,ProcedureService $procediproService,
                            CitasRepository $citasRepository,
                            ProfesionalService $profesionalService,
                            QueueService $queueService
                            )
    {
        $this->responseManager=$responseManager;
        $this->citasModel=$citasModel;
        $this->citasRepository=$citasRepository;
        $this->procediproService=$procediproService;
        $this->profesionalService=$profesionalService;
        $this->queueService=$queueService;

    }
    public function create(CreateCitasDto $dto){
        if(!$this->citasRepository->validateHistAuth(
            history:$dto->getHistCode(),
            n_autoriz:$dto->getAutoriz()
        )){
            throw new BadRequestException("La orden ingresada no pertenece al usuario seleccionado",400);
        }
        $startDate=$dto->getStartDate();
        if($dto->isSingleAppo()){
            $weekDaysNew=CitasDomain::completeScheduleSingleAppo(startDate:$startDate);
            $dto->setDaysWeek(new:$weekDaysNew);
            $dto->setTotalCitas(total:1);

        }
        $weekDays=$dto->getDaysWeek();
        $daysName=$dto->getDaysWeekNames();

        $procediproName=$dto->getProcedipro();
        $procedipro=$this->procediproService->getProceduraByName(name:$procediproName);
        $procediproIsContable=$procedipro->isContable();
        
        CitasDomain::validateStartDateInSchedule(
            startDate:$startDate,
            allowedDays:$daysName,
            scheduleDays:$weekDays

        );
        CitasDomain::checkRememberWhatsWhitObservations(
            whatsappRemember:$dto->isWhatsappNotificable(),
            observaId:$dto->getObservaId()
        );
        $dispo=$this->getAvailabilityForCreation(
            auth:$dto->getAutoriz(),
            codeCup:$dto->getCodCup(),
            history:$dto->getHistCode()
        );
        if($dispo->disponibles==0){
            throw new BadRequestException("La autorizacion seleccionada con el codigo Cup seleccionada no tienen disponibilidad de agenda",400);
        }
        

        $sessionsToSave=CitasDomain::getNumSessionsToSave(
            availableSessions:(int) $dispo->disponibles,
            isProcediproContable:$procediproIsContable,
            requestedSessions:$dto->getTotalCitas()
        );

        
        $diferenceBeetwenDays=CitasDomain::getDifBeetwenDays(weekDays:$daysName);
        $schedule=CitasDomain::CreateSchedule(
            sessionsAvaibles:$sessionsToSave,
            sessionDuration:$dto->getDuractionAppo(),
            startDate:$startDate,
            weekDays:$weekDays,
            diferenceDays:$diferenceBeetwenDays,
            weekDaysKeys:$daysName
        );
        if(empty($schedule)){
            throw new BadRequestException("Error: no se pueden cargar mas citas a esta order",400);
        }
        CitasDomain::validateLimitExpireAuth(schedule:$schedule,expireAuthDate:$dto->getExpireDateAuth());


        $indexDay=DateManager::getIndexDayByDayList(list:$dto->getDaysWeekNames());
        $scheduleProfesional=$this->profesionalService->findSchedulePorfesionalByDays(
            cedula: $dto->getCedula(),
            weekDays:$indexDay
        );

        $validated=CitasDomain::validateSchedule(scheduleNewAppos:$schedule,scheduleProfesional:$scheduleProfesional);

        /**COMENTADO HASTA RECIBIR POLITICAS DE EXCEPCIONES */
        $startDateFirstAppo=$schedule[0];
        $finishDate=$schedule[count($schedule) - 1];

        $calendarClient=$this->citasRepository->getLigtCalendar(
           clientCode:$dto->getHistCode(),
            from:$startDateFirstAppo->format('Y-d-m'),
            to:$finishDate->format('Y-d-m')
        );
        CitasDomain::validateDisponibilityClient(scheduleNewAppos:$schedule,
                                                scheduleClient:$calendarClient,
                                                sessionDuration:$dto->getDuractionAppo(),
                                                procedipro:$dto->getProcedipro());
        $familyId = (string) Str::uuid();
        $dto->setFamilyId($familyId);
        $apposInDto = [];
        foreach ($schedule as $session) {
            $dtoSingleAppo = $dto->copy();
            AppoimentsMapper::AppoDtoToAppo(
                dto: $dtoSingleAppo,
                session: $session,
                schedule: $validated[$session->format('Y-m-d H:i')]
            );

            $apposInDto[] = $dtoSingleAppo;
        }
        
        $this->citasRepository->create(ApposDto: $apposInDto);
        $appos=$this->citasRepository->getFamilyAppos($familyId);
        $idsAppos = collect($appos)->pluck('id')->toArray();
        $audit=CitasDomain::buildAuditMsm(
            user:$dto->getUserRequest()->getUsername(),
            client:$dto->getClient(),
            schedule:$schedule,
            profesional:$dto->getProfesional(),
            autoriz:$dto->getAutoriz(),
            family:$familyId,
            ids:$idsAppos,
            
        );
        $this->queueService->publish(
            $this->buildMsmAudit(
                action:$audit,
                userRequesting:$dto->getUserRequest()
            )
        );
        return $this->responseManager->created($appos);

        
    }
    private function getAvailabilityForCreation(string $auth,string $codeCup,string $history):stdClass{
        return $this->citasRepository->getApposAvaiables(autoriz:$auth,cupCode:$codeCup,history:$history);
    }
    public function deleteCitaById(DeleteAppoDto $dto){
        $citas= $this->citasRepository->getApposByIds(ids:$dto->getIds());
        if(empty($citas)){
            throw new BadRequestException("la cita que desea eliminar no existe o no esta disponible para eliminar",400);
        }
        $cita=$citas[0];
        $citasDeleted=$this->citasRepository->deleteByIds(ids:$dto->getIds());
        if ($citasDeleted==0){
           throw new BadRequestException("no es posible eliminar esta sección",400);
        }
        $msm = CitasDomain::buildAuditMsmDlete(
            user:$dto->getUserRequest()->getUsername(),
            ids:$dto->getIds(),
            cliente:$cita->usuario,
            profesional:$cita->profesional
        );
        $this->queueService->publish(
            $this->buildMsmAudit(
                action:$msm,
                userRequesting:$dto->getUserRequest()
            )
        );
        return $this->responseManager->success([]);
    }

    public function getCitasById($id){
        $cita=$this->citasRepository->getById(id:$id);
        if(empty($cita)){
            throw new NotFoundException("la cita actual no fue encontrada",404);
        }

        return $this->responseManager->success($cita);
    }
    public function corfirmateGroupSessions($request){

        CitasRequests::ValidateCitaSessionsIds($request,'confirmar');
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
    public function CancelGroupSsessions(array $request,UserRequesting $userRequesting){
        CitasRequests::ValidateCitaSessionsIds($request,"cancelar");
        $citasCanceled=$this->sendQueryToCancelGroupSessions($request);
        
        $meanCancel=$request['meanCancel'];
        $dateCita = $request['fecha_cita'];
        $dateCancelation = Carbon::now()->format('Y-m-d H:i:s');
        $idsToCancel = str_replace('|||', ',', $request['ids']);


        if ($meanCancel=="mc") {
            $msm = "El usuario {$userRequesting->getUsername()} ha cancelado una las citas con ids  {$idsToCancel} asignada para la fecha {$dateCita} el dia {$dateCancelation}";
            $this->queueService->publish(
                $this->buildMsmAudit(
                    action:$msm,
                    userRequesting:$userRequesting
                )
            );
        }

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
        CitasRequests::validateDataToChangeProfesional($request);
        $citasChanged=$this->sendQueryToChangeProfesionalsCitas($request);
        //event (new ChangeProfesionalEvent($request));
        return $this->responseManager->success($citasChanged);
        
    }
    public function getCitasClient($clientCode){
        if(empty(trim($clientCode))){
            throw new BadRequestException("El código de cliente debe ser valido",400);
        }
        $citas=$this->sendQueryToGetCitasClient($clientCode);
        $calendarMapped=$this->mapCalendarClient($citas);
        $citasGroupedBySessions=$this->GroupCitasBysessions($calendarMapped);
        $citasFlated = collect($citasGroupedBySessions)
            ->flatMap(fn ($items) => $items)
            ->values()
            ->toArray();
        return $this->responseManager->success($citasFlated);
    }
    public function getHistoryCitasClient($clientCode){
        if(empty(trim($clientCode))){
            throw new BadRequestException("El código de cliente debe ser valido",400);
        }
        $citas=$this->sendQueryToGetToCitasHistoryClient($clientCode);
        $calendarMapped=$this->mapCalendarClient($citas);
        $citasGroupedBySessions=$this->GroupCitasBysessions($calendarMapped);
        $citasFlated = collect($citasGroupedBySessions)
            ->flatMap(fn ($items) => $items)
            ->values()
            ->toArray();
        return $this->responseManager->success($citasFlated);
    }

    public function cloneScheduleProfesional(CloneCalendarDto $dto): array
    {

        $fromString = $dto->getFrom();
        $toString = $dto->getTo();
        $startString = $dto->getStart();
        $cedula = $dto->getCedula();
        $usuario = $dto->getUserRequest()->getUsername();
        $cedulaUsuario = $dto->getUserRequest()->getCedula();
        $profesional = $dto->getProfesional();

        $fromDate = Carbon::parse($fromString);
        $startDate = Carbon::parse($startString);
        $toDate=Carbon::parse($toString);
        $diffDays = $startDate->diffInDays($fromDate);
        $citasInfo = $this->citasModel->getInfoAppoimnetsToClone($cedula, $fromDate->format('Y-m-d'), $toDate->format('Y-m-d'));
        $appoimentsToClone = $citasInfo['appoiments'];
        $appoimentsAvailability = $citasInfo['availability'];

        $availabilityMap = [];
        foreach ($appoimentsAvailability as $item) {
            $availabilityMap[$item->autorizacion] = $item->disponibles;
        }

        $ids = [];
        foreach ($appoimentsToClone as $appoiment) {
            $authorizationAndOrder = $appoiment->autoriz . '|||' . $appoiment->tiempo;


            if (!isset($availabilityMap[$authorizationAndOrder]) || $availabilityMap[$authorizationAndOrder] <= 0) {
                continue;
            }
            $dateAppoiment = Carbon::parse($appoiment->fecha);
            $dateNewAppoiment = $dateAppoiment->copy()->addDays($diffDays);

            if (DateManager::isHoliday($dateNewAppoiment)) {
                continue;
            }               
            $appoimentMap = AppoimentsMapper::mapAppoimentToClone($appoiment, $usuario, $dateNewAppoiment,$cedulaUsuario);
            $idNewAppoiment = $this->citasModel->saveAppoimentClone($appoimentMap);
            $availabilityMap[$authorizationAndOrder]--;
            $ids[] = $idNewAppoiment;
        }


        $newsAppoiments=$this->citasModel->getApoimentByIds($ids);
        $appoimentsMapped= $this->mapCalendarClient($newsAppoiments);
        
        $audit=CitasDomain::buildAuditMsmClone(
                user:$usuario,
                profesional:$profesional,
                from:$fromDate,
                to:$toDate,
                start:$startDate,
                ids:$ids
            );
        $this->queueService->publish(
            $this->buildMsmAudit(
                action:$audit,
                userRequesting:$dto->getUserRequest()
            )
        );
        return $this->responseManager->success($appoimentsMapped);
    }
    public function deleteScheduleProfesional($request){
        //citasRequests::validateDataToCloneSchedule($request);
        $fromString = $request['from'];
        $toString = $request['to'];
        $cedula = $request['cedula'];

        $fromDate = Carbon::parse($fromString)->format('Y-m-d');
        $toDate=Carbon::parse($toString)->addDay()->format('Y-m-d');

        $appoimentsDeleted=$this->citasModel->deleteCalendarProfesional(from:$fromDate,to:$toDate,cedulaProfesional:$cedula);
        if($appoimentsDeleted==0){
            throw new NotFoundException("El profesional no registra Citas en este Rango de Tiempo",404);
        }
        return $this->responseManager->success("Fueron Eliminadas {$appoimentsDeleted} citas");

    }
    private function buildMsmAudit(string $action,UserRequesting $userRequesting,string $modulo = 'citas'):MessageQueue{
        return AuditMessageQueueBuilder::create()->withData([
            'audit'=>$action,
            'cedula'=>$userRequesting->getCedula(),
            'modulo'=>$modulo
        ])->build();
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
                        ci.mean_cancel as medio_cancelacion,
                        pro.recordatorio_whatsapp,
                        pro.duraccion AS duracion,
                        se.nombre AS sede,
						em.enombre AS profesional,
						cli.nombre AS cliente,
                        cli.cel AS celular,
						oc.nombre AS nombre_plantilla_observacion
                    FROM citas_canceladas cica
                    INNER JOIN citas ci ON ci.id = cica.id_example
                    INNER JOIN procedipro pro ON pro.nombre=ci.procedipro
                    INNER JOIN sede se ON se.cod=ci.sede
                    INNER JOIN emplea em ON em.ecc = ci.cedprof
                    INNER JOIN cliente cli ON cli.codigo =ci.nro_hist
					INNER JOIN observa_citas oc ON oc.id=CAST(ci.observaciones_mc AS INT)
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
        $meanCancel=$request['meanCancel'];
        $dateCita = $request['fecha_cita'];
        $dateCancelation = Carbon::now()->format('Y-m-d H:i:s');
        $idsToCancel = $request['ids'];
        $idExample = $this->getIdExample($idsToCancel);
        $razon = $request['razon'];
        $numSessionsCanceled = count(explode('|||', $idsToCancel));
    
        $idsArray = explode('|||', $idsToCancel);  
        $idsForQuery = array_map('intval', $idsArray);  
         
    
        try {
            $citasCanceled = DB::transaction(function () use ($razon, $dateCancelation, $idsForQuery, $idsToCancel, $idExample, $numSessionsCanceled, $dateCita,$meanCancel) {
                foreach ($idsForQuery as $id){
                    $citasCanceled = DB::update(
                        "
                        UPDATE citas 
                        SET cancelada = '1', 
                            realizar = ?, 
                            fec_can = CONVERT(smalldatetime, ?, 120) ,
                            mean_cancel=?
                        WHERE id= ? 
                        AND asistio = '0' 
                        AND na = '0'",
                        array_merge([$razon, $dateCancelation,$meanCancel],[$id] )
                    );
                }

                DB::insert(
                    "
                    INSERT INTO citas_canceladas 
                    (ids_sessions, id_example, num_sessions_canceled, date_cita_canceled)
                    VALUES (?, ?, ?, CONVERT(smalldatetime, ?, 120))",
                    [$idsToCancel, $idExample, $numSessionsCanceled, $dateCita]
                    );
                
        
                
                return $citasCanceled;
            });
        
            return $this->citasRepository->getApposByIds($idsForQuery);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    private function getIdExample($ids){
        return explode('|||', $ids)[0];
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
        $now=Carbon::now()->setTime(hour: 0, minute: 0)->format('Y-m-d H:i:s');
        
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
                    procedipro pro ON pro.nombre = ci.procedipro
                INNER JOIN 
                    emplea em ON em.ecc = ci.cedprof
                WHERE 
                    ci.nro_hist = ?
                    AND ci.fecha >= CONVERT(date, GETDATE(), 120)

                ORDER BY 
                    ci.fecha ASC;
                ",[$ClientCod,$now]);
            return $citas;
         }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
         }
    }
    private function sendQueryToGetToCitasHistoryClient($ClientCod){
        $now = Carbon::now()->setTime(0, 0)->format('Y-m-d H:i:s');
        
        try {
            $citas = DB::select(
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
                    ci.direccion_cita AS direcion,
                    ci.na AS no_asistida,
                    pro.duraccion AS duracion,
                    em.enombre AS profesional
                FROM 
                    citas ci
                INNER JOIN 
                    procedipro pro ON pro.nombre = ci.procedipro
                INNER JOIN 
                    emplea em ON em.ecc = ci.cedprof
                WHERE 
                    ci.nro_hist = ?
                    AND ci.fecha BETWEEN 
                        DATEADD(DAY,-90,GETDATE())
                        AND CONVERT(date, DATEADD(DAY,1,GETDATE()), 120)
                ORDER BY 
                    ci.fecha DESC;
                ", [$ClientCod, $now]);
            
            return $citas;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    private function GroupCitasBysessions($citas,$deleteKeys=[]){
        $citasMapper=new CalendarClientMapper();
        return $citasMapper->groupCitasBySessions($citas,deleteKeys:$deleteKeys);
    }


    private function mapCalendarClient($unMappedCalendar){
        $calendarMapper=new CalendarProfesionalMapper();
        return $calendarMapper->map($unMappedCalendar);

    }

 

}