<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mappers\HistoryChatBotMapper;
use App\Requests\ClientRequest;
use App\utils\CelNumberManager;
use App\utils\DateManager;
use App\utils\PasswordGenerator;
use Illuminate\Support\Facades\DB;
use App\utils\ResponseManager;

use Illuminate\Support\Carbon;


class ClientService{
    private $responseManager;
    private $emailService;
    private $whatsappService;

    public function __construct(ResponseManager $responseManager,EmailService $emailService, WhatsappService $whatsappService){
        $this->responseManager=$responseManager;
        $this->emailService=$emailService;
        $this->whatsappService=$whatsappService;

    }
    public function searchClientByString($string){
        $string = trim($string);
        if (empty($string)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }
        $searchName = strtoupper($string);

        try{
            $clients = DB::select("
            SELECT TOP 20 codigo, nombre
            FROM cliente WHERE (ok_ent = 0 AND ( nombre LIKE ? )) AND activo = 1 ORDER BY nombre
            ", ["%{$searchName}%"]);
            
            if(!$clients){
                throw new NotFoundException("no se encontraron clientes",404);
            }

        
            return $this->responseManager->success($clients);
       
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }

    }
    

    public function getDataClientByHistoryId($request)
    {

        ClientRequest::historyIdValidate($request);
        $client = $this->getClientByHistoryId($request['historyId']);
        if (empty($client)) {
            throw new NotFoundException("Información de usuario no encontrada", 404);
        }
        $clientWithAge = $this->calculateAge($client);
        return $this->responseManager->success($clientWithAge);
    }

    public function getAuthorizationsByCliencode($clientCode){
        $clientCode=trim($clientCode);
        if (empty($clientCode)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }

        $Authorizations=$this->getAuthorizationsByClient($clientCode);
        
        if(empty($Authorizations)){
            throw new NotFoundException("El usuario no registra autorizaciones",404);
        }
        return $this->responseManager->success($Authorizations);
    }
    public function getDataFromAuthorizationCode($authorizationCode){
        $authorizationCode=trim($authorizationCode);

        if (empty($authorizationCode)){
            throw new BadRequestException("el codigo de authorization debe ser un codigo valido",400);
        }
        
        $data=$this->getDataAuthorization($authorizationCode);
        return $this->responseManager->success($data);
    }
    public function GnerateNewPasswordClient($request){
        ClientRequest::ValidateDataToRequestPassword($request);
        $this->CheckWayToSendPasswordIsSelected($request);
        $client=$this->getClientByIdentiy($request);
        $request['clientIdentity']=$client[0]->codigo;
        if(empty($client)){
            throw new NotFoundException('No se han encontrado usuarios',404);
        }

        if($request['sendPasswordToEmail'] && !$client[0]->email){
            throw new BadRequestException("el usuario no registra Email",400);
        }
        if($request['sendPasswordToMobile'] && !$client[0]->thelephoneNumber){
            throw new BadRequestException("el usuario no registra contacto",400);
        }
        $password=PasswordGenerator::generatePassword();
        $clientsUpdate=$this->saveNewPassword($password,$request);
        if(empty($clientsUpdate)){
            throw new NotFoundException("no se ha encontrado usuario a actualizar",404);
        }
        $this->sendMesaggeWithNewPassword($request,$client[0],$password);
        return $this->responseManager->success($client);
    }
    public function setPasswordClient($request){
        ClientRequest::ValidateNewPassword($request);
        $clientUpdated=$this->saveNewPassword($request['password'],$request);
        if ($clientUpdated==0){
            throw new NotFoundException("Cliente no encontrado",404);
        }
        return $this->responseManager->success($clientUpdated);
        
    }
    public function updateClient($request){
        ClientRequest::validateDataToUpdateClient($request);
        $clientUpdated=$this->sendQueryToUpdateClient($request);
        if (empty($clientUpdated)){
            throw new NotFoundException("Cliente no encontrado",404);
        }
        return $this->responseManager->success($clientUpdated);

    }
    public function getHistoryChatBotByClientCod($codigoClient){
        if(empty($codigoClient)){
            throw new BadRequestException("No se ha proporcionado ni un cliente",400);
        }
        $celClient=$this->GetNumberCelClient($codigoClient);
        if (empty($celClient)){
            throw new NotFoundException("El cliente no registra celular",404);
        }
        $celCleaned=$this->cleanerCels($celClient->cel);

        $history=$this->getHistoryWhatsapp($celCleaned);
        if (empty($history)){
            throw new NotFoundException("El usuario no guarda registro de chat",404);
        }

        $historyMapped=$this->MapHistory($history);


        return $this->responseManager->success($historyMapped);
        
    }
    public function getForbidensBlocksClient($request)
    {

        $blocksProhibided = $this->getForbbidentsBlocks($request);
        $events = [];
        foreach ($blocksProhibided as $block) {
            $mappedEvents = $this->mapBlocksAndDays($block, $request);
            if (is_array($mappedEvents)) {
                $events = array_merge($events, $mappedEvents);
            }
        }
        return $this->responseManager->success($events);
    }
    private function getClientByHistoryId($historyId)
    {
        try {
            $clientInfo = DB::select("
            SELECT 
                cli.codigo, 
                cli.nombre, 
                cli.nit_cli, 
                cli.f_nacio, 
                cli.sexo, 
                cli.direcc,
                cli.barrio, 
                cli.cel,
                cli.codent AS cod_entidad,
                cli.codent2 AS convenio,
                mun.nombre AS municipio, 
                ent.clase AS entidad 
                FROM cliente cli 
                INNER JOIN entidades ent ON ent.codigo = cli.codent2 
                INNER JOIN municipio mun ON mun.codigo = cli.cod_ciudad 
                WHERE cli.codigo = ?
            ", [$historyId]);


            if (empty($clientInfo)) {
                throw new NotFoundException("Información de usuario no encontrada", 404);
            }


            return $clientInfo;

        } catch (\Exception $e) {

            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    private function getAuthorizationsByClient($clientCode){
        try{
            $authorizations=DB::select("
                SELECT TOP 20 n_autoriza, fecha, f_vence, entidad AS codent, paquete AS codent2, observa 
                FROM  autoriza WHERE historia = ? and  (anulada = 0  OR (suspendida = 1 and anulada = 1)) 
                GROUP BY n_autoriza, fecha, f_vence, entidad, paquete, observa  ORDER BY fecha DESC
            ",[$clientCode]);
            return $authorizations;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);        }
    }
    private function getDataAuthorization($authorizationCode){
        try{
            $dataAuthorization=DB::select(
                "
                SELECT au.n_autoriza, au.procedi AS tiempo, pro.descrip AS procedim, au.cantidad
                FROM autoriza au
                INNER JOIN procdent pro ON au.procedi = pro.codigo  
                WHERE n_autoriza = ?
                AND pro.cod_enti = (
                    SELECT TOP 1 tarifa
                    FROM entidades en
                    INNER JOIN procdent pro ON en.tarifa = pro.cod_enti
                    WHERE en.admini = au.entidad
                    GROUP BY tarifa
                )
                UNION ALL
                SELECT n_autoriza, procedi AS tiempo, nombre AS procedim, cantidad
                FROM autorizad
                WHERE n_autoriza = ?
                ",[$authorizationCode,$authorizationCode]
            );
            return $dataAuthorization;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    

    private function calculateAge($client)
    {

        $birthDateClient = Carbon::parse($client[0]->f_nacio);
        $now = Carbon::now();
        $age = $now->diffInYears($birthDateClient);
        $client[0]->f_nacio = $age. ' años';

        return $client;
    }

    private function CheckWayToSendPasswordIsSelected($request){
        $emailMean=$request['sendPasswordToEmail'];
        $mobileMean=$request['sendPasswordToMobile'];

        if(!$emailMean && !$mobileMean){
            throw new BadRequestException('No se ha seleccionado un medio de envio del mensaje',400);
        }
    }
    private function getClientByIdentiy($request)
    {

        $identityClient = $request['clientIdentity'];
    
        try {
            $client = DB::select("
                SELECT TOP 1 
                nombre,
                codigo,
                email as email,
                cel AS thelephoneNumber
                FROM cliente
                WHERE nit_cli = ? 
            ", [$identityClient]);
    
            return $client;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function saveNewPassword(string $newPassword, array $request)
    {   

        $codigoClient = $request['clientIdentity'];
         
        $newPasswordEncrypted = bcrypt($newPassword);
    
        try {
            $clientsWithNewPassword = DB::update(
                "
                UPDATE cliente2 
                SET user_password_mc = ?
                WHERE codigo = ?
                ",
                [
                    $newPasswordEncrypted,
                    $codigoClient
                ]
            );
    
            return $clientsWithNewPassword;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    
    private function sendMesaggeWithNewPassword(Array $request,$client,string $newPassword){
        $sendToEmail=$request['sendPasswordToEmail'];
        $email=$client->email;
        $telephoneNumber=$client->thelephoneNumber;
        $nameClient=$client->nombre;

        $sendToEmail?$this->sendRecoveryEmail($nameClient,$newPassword,$email):
                    $this->sendRecoveryWhatsapp($nameClient,$newPassword,$telephoneNumber);


    }
    private function sendRecoveryEmail($username, $newPassword, $email) {
        if (!$this->emailService->sendEmail($username, $newPassword, $email)) {
            throw new ServerErrorException("No fue posible conectar con el servicio de email", 500);
        }
    }
    private function sendRecoveryWhatsapp($username,$newPassword,$thelephoneNumber){
        $telephoneNumberCleaned=$this->cleanThelphoneNumber($thelephoneNumber);
        $payload=$this->buildPayload($username,$newPassword,$telephoneNumberCleaned);
        $this->whatsappService->sendMessageToRetrievePassword($payload);

    }
    private function cleanThelphoneNumber(string $telephoneNumber){
        return substr($telephoneNumber, 0, 10);
    }
    private function buildPayload($username,$newPassword,$thelephoneNumber){
        return [

            'telephoneNumber' => $thelephoneNumber,
            'password'=>$newPassword,
            'clientName'=>$username
        ];
    }

    private function sendQueryToUpdateClient($request)
    {
        // Extraer el código del cliente
        $clientCod = $request['clientCod'];
        unset($request['clientCod']);
    
        
        $columnsToUpdate = array_keys($request);
        $valuesToUpdate = array_values($request);
    
         
        $placeholders = implode(', ', array_map(fn($column) => "$column = ?", $columnsToUpdate));
    
        try{
            $clientsUpdate=DB::update(
                "
                UPDATE cliente SET $placeholders WHERE codigo = ?
                ",
                array_merge($valuesToUpdate, [$clientCod])
            );
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
        return $clientsUpdate;

    }
    private function GetNumberCelClient($codigoClient){
        try{
            $cel=DB::select(
                "
                    select cel from cliente where codigo= ?
                ",[$codigoClient]
            );
            return $cel[0];
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function cleanerCels($dirttyCel){
        $celCleaned= CelNumberManager::chooseTelephoneNumber($dirttyCel);
        if(empty($celCleaned)){
            throw new NotFoundException("El usuario no registra numero de celular valido",404);
        }
        return $celCleaned;
    }
    private function getHistoryWhatsapp($cel){
        try{
            return $this->whatsappService->getHistoryWhatsapCel($cel);
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function MapHistory($history){
        return HistoryChatBotMapper::map($history);
    }
    private function getForbbidentsBlocks($request){
        $profesionalCed=$request['cedula'];
        try{
            $scheduleforbident=DB::select(
                "
                SELECT hora_inicio,hora_fin,dias 
                FROM bloques_pro
                WHERE profesional_ced = ?
                ",[$profesionalCed]
            );
            return $scheduleforbident;

        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function mapBlocksAndDays($block, $request)
    {   
        $startDate = Carbon::parse($request['start']);
        $endDate = Carbon::parse($request['end']);

        $days = array_map(fn($day) => strtolower($day), explode('|||', $block->dias));
    
        $startDateCopy = $startDate->copy();
         
        $startHour = $this->getMinutesSinceStartDay($block->hora_inicio);
        $endHour = $this->getMinutesSinceStartDay($block->hora_fin);
    
        $eventsToSchedule = [];
        while ($startDateCopy->isBefore($endDate)) {
 
            $dayDate = DateManager::getDayByDate($startDateCopy);

            if (in_array(strtolower($dayDate), $days)) {
                $dateDay = $startDateCopy->copy();
                $dateDayOnly = DateManager::getDateInSmallDateTime($dateDay);
                $dateDayOnly=Carbon::parse($dateDayOnly);


                $event = [
                    'start' => $dateDayOnly->copy()->addMinutes($startHour),   
                    'end' => $dateDayOnly->copy()->addMinutes($endHour)     ,
                    'title' => 'no disponible',
                    'color' => '#ccc'
                ];
    
                 
                $eventsToSchedule[] = $event;  
                $startDateCopy->addDay();
            } else {
                
                $startDateCopy->addDay();
            }
        }
    
        return $eventsToSchedule;  
    }
    private function getMinutesSinceStartDay(string $hour){
        $hornIn24Format=DateManager::ConvertHourTo24Format($hour);
        return DateManager::CalculateMinutesSinceStartOfDay($hornIn24Format);
    }
    
    
    
    
    
}