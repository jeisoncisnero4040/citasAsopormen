<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Requests\ClientRequest;
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
        $this->saveNewPassword($password,$request);
        $this->sendMesaggeWithNewPassword($request,$client[0],$password);
        return $this->responseManager->success($client);
    }
    public function setPasswordClient($request){
        ClientRequest::ValidateNewPassword($request);
        $clientUpdated=$this->sendQueryToupdatePasswordClient($request);
        if ($clientUpdated==0){
            throw new NotFoundException("Cliente no encontrado",404);
        }
        return $this->responseManager->success($clientUpdated);
        
    }
    public function updateClient($request){
        ClientRequest::validateDataToUpdateClient($request);
        $clientUpdated=$this->sendQueryToUpdateClient($request);
        if ($clientUpdated==0){
            throw new NotFoundException("Cliente no encontrado",404);
        }
        return $this->responseManager->success($clientUpdated);

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

                ent.clase AS entidad 
                FROM cliente cli 
                INNER JOIN entidades ent ON ent.codigo = cli.codent2 

                WHERE cli.codigo =  ?", [$historyId]);
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
    private function saveNewPassword(string $newPassword,Array $request)
    {
        $codigoClient=$request['clientIdentity'];
        $newPasswordEncrypted=bcrypt($newPassword);
    
        try {
            
            $clientsWithNewPassword = DB::update(
                "
                UPDATE cliente 
                SET password = ?
                WHERE nit_cli = ?
                ", [
                    $newPasswordEncrypted,    
                    $codigoClient 
                ]
            );
    
            return $clientsWithNewPassword !=0; 
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
    private function sendQueryToupdatePasswordClient($request){
        $password=$request['password'];
        $client=$request['clientCod'];

        $passwordWithBycript=bcrypt($password);
        try{
            $clientUpdated=DB::update("
                UPDATE cliente
                SET password = ? 
                WHERE  codigo = ?
            ",[$passwordWithBycript,$client]);
            return $clientUpdated;

        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
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
    
    
}