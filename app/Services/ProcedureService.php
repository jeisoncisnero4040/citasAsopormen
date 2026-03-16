<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\ProcedureModel;
use App\Repositories\ProcediproRepository;
use App\utils\ResponseManager;
use Illuminate\Support\Facades\DB;

class ProcedureService{

    private $responseManager;
    private ProcediproRepository $repo;
    

    public function __construct(ResponseManager $responseManager,ProcediproRepository $repo)
    {
        $this->responseManager=$responseManager;

        $this->repo=$repo;

    }
    public function getAllProcedures(){
        return $this->responseManager->success(
            $this->getProcedures(),200
        );
    }
    public function getProceduraByName(string $name):ProcedureModel{
        $string = trim($name);
        if (empty($string)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }
        $string=strtoupper($string);
        return $this->repo->getProcedureByName(name:$string);
    }
    private function getProcedures() {

    }
    public function searchProcedureByString($string){
        $string = trim($string);
        if (empty($string)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }
        

        $string=strtoupper($string);

        $procedures = DB::select("
            SELECT TOP 10 pro.id, pro.nombre, pro.duraccion,pro.recordatorio_whatsapp
            FROM procedipro pro
            WHERE pro.nombre LIKE ?
            ORDER BY pro.nombre
        ", ["%{$string}%"]);

        if (empty($procedures)) {
            throw new NotFoundException("no se han encontado registros",404);
        }

        foreach($procedures as $procedure){
            $procedure->recordatorio_whatsapp=(bool)(int)$procedure->recordatorio_whatsapp;
        }
        
        return $this->responseManager->success($procedures);
         
    }
    public function find(array $data){

        $nombre = $data['nombre'];
        $string = trim($nombre);
        if (empty($string)) {
            throw new BadRequestException("el parametro de búsqueda debe ser válido",400);
        }
        $string=strtoupper($string);

        $procedures = DB::select("
            SELECT TOP 1 pro.id, pro.nombre, pro.duraccion,pro.recordatorio_whatsapp
            FROM procedipro pro
            WHERE pro.nombre = ?
            ORDER BY pro.nombre
        ", [$string]);

        if (empty($procedures)) {
            throw new NotFoundException("no se han encontado registros",404);
        }
        foreach($procedures as $procedure){
            $procedure->recordatorio_whatsapp=(bool)(int)$procedure->recordatorio_whatsapp;
        }
        return $this->responseManager->success($procedures);
    }
    
}       