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
                cli.codent2 AS covenio,
                ccb.ciudad AS municipio, 
                ent.clase AS entidad 
                FROM cliente cli 
                INNER JOIN entidades ent ON ent.codigo = cli.codent2 
                INNER JOIN codigosciudadesdebancos ccb ON ccb.codigociudad = cli.cod_ciudad 
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

    private function calculateAge($client)
    {

        $birthDateClient = Carbon::parse($client[0]->f_nacio);
        $now = Carbon::now();
        $age = $now->diffInYears($birthDateClient);
        $client[0]->f_nacio = $age. ' años';

        return $client;
    }
    
}