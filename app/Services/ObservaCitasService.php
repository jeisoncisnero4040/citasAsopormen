<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\utils\ResponseManager;
use Illuminate\Support\Facades\DB;

class ObservaCitasService{
    private $responseManager;
    public function __construct(ResponseManager $responseManager)
    {
        $this->responseManager=$responseManager;
    }
    public function getObservationName(){
        $observaCitas=$this->sendQueryToGetAllObservationsCitas();
        if(empty($observaCitas)){
            throw new NotFoundException("no sencontraron observaciones",404);
        }
        return $this->responseManager->success($observaCitas);
    }
    public function getObservationContentById($id){
        $observationContent=$this->sentQueryToGetObservationContentById($id);
        if(empty($observationContent)){
            throw new NotFoundException("No se encontro observacion",404);
        }
        return $this->responseManager->success($observationContent[0]);
    }
    private function sendQueryToGetAllObservationsCitas(){
        try{
            $observaCitas=DB::select("
                SELECT 
                id,
                nombre
                FROM observa_citas
            ");
            return $observaCitas;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function sentQueryToGetObservationContentById($id){
        try{
            $observation=DB::select("
                SELECT 
                contenido
                FROM observa_citas
                Where id= ?
            ",[(integer)$id]);
            return $observation;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
}