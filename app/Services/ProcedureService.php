<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\ProcedureModel;
use App\utils\ResponseManager;

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
    
}       