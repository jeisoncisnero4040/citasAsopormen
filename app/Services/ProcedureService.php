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
    private function getProcedures(){
        $procedures=$this->procedureModel::select('id','nombre')->get();
        if($procedures->isEmpty()){
            throw new NotFoundException("lista de procesos no encontrada",404);
        }
        return $procedures;
    }
}       