<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\ProcedureModel;
use App\utils\ResponseManager;
use Illuminate\Support\Facades\DB;

class ProcedureService{
    private $procedureModel;
    private $responseManager;

    public function __construct(ProcedureModel $procedureModel, ResponseManager $responseManager)
    {
        $this->responseManager=$responseManager;
        $this->procedureModel=$procedureModel;

    }
    public function getAllProcedures(){
        return $this->responseManager->success(
            $this->getProcedures(),200
        );
    }
    private function getProcedures() {
        $procedures = $this->procedureModel::select('id', 'nombre', 'duraccion', 'recordatorio_whatsapp')->get();
        
        if ($procedures->isEmpty()) {
            throw new NotFoundException("Lista de procesos no encontrada", 404);
        }
    
         
        $procedures->transform(function($procedure) {
            $procedure->recordatorio_whatsapp = (bool) $procedure->recordatorio_whatsapp;
            return $procedure;
        });
    
        return $procedures;
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

        return  $this->responseManager->success($procedures);
         
    }
    
}       