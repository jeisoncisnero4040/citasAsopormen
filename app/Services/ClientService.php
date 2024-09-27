<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Requests\ClientRequest;
use Illuminate\Support\Facades\DB;
use App\utils\ResponseManager;
use Illuminate\Support\Carbon;
 
class ClientService{
    private $responseManager;
    public function __construct(ResponseManager $responseManager){
        $this->responseManager=$responseManager;

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
                WHERE cli.codigo = ?
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
    
}