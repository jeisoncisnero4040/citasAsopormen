<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Models\CitasModel;
use App\utils\ResponseManager;
use App\Validators\CitaValidator;
use Carbon\Carbon;

class CitasService{
    private $citasModel;
    private $responseManager;
    const TEST_PHONE_NUMBER = '3222551222';
    public function __construct(CitasModel $citasModel,ResponseManager $responseManager)
    {
        $this->citasModel=$citasModel;
        $this->responseManager=$responseManager;
 
    }   
    public function sendCitaToWait($request){
        CitaValidator::ValidateCitaAndNumberClient($request);
        $citasId=$request['session_ids'];
        $numberClient=$request['telephone_number'];
        $dateCita=$request['date'];
        $observation=$request['observations'];

        $cita=[
            'number'=>$numberClient,
            'cita_ids'=>$citasId,
            'date_cita'=>$dateCita,
            'observations' =>$observation

        ];
        try {

            //$citaInBd=$this->checkCitaAlreadyWorked($citasId,$numberClient);
            $citaInBd=false;
            if (!$citaInBd){
                $citaWaiting = $this->citasModel::create($cita);
                return $this->responseManager->created($citaWaiting);
            }
            throw new ServerErrorException("Cita already worked",500);
        } catch (\Exception $e) {
            
            throw new ServerErrorException($e->getMessage(),500);
        }
    }

    public function findLaterCitaAvaiableByTelephoneNumber($telephone){
        return CitasModel::where('number',$telephone)
                        ->where('accepted',0)
                        ->where('canceled',0)
                        ->orderBy('id', 'desc') 
                        ->first();
    }
    public function verifyCitaOnWaitMode($telephone,$cita){

        $citas_id=$cita->cita_ids;
        return CitasModel::where('number', $telephone)
                        ->where('waiting',1)
                        ->where('cita_ids',$citas_id)
                        ->orderBy('id', 'desc') 
                        ->first();
    }
    public function  updateCita($cita,$column){
        $cita_ids = $cita->cita_ids;
        $numberTelephoneClient = $cita->number;
        try {
                $this->citasModel::where('cita_ids', $cita_ids)
                ->where('number', $numberTelephoneClient)
                ->update([$column=> 1]);   
    
        } catch (\Exception $e) {
            error_log($e->getMessage());   
        }
    }
    public function verifyCitaUpdateFiveMinsBefore($cita){
        $now=Carbon::now();
        $timeCitaUpdated=$cita->updated_at;
        $differenceInMinutes = $now->diffInMinutes($timeCitaUpdated);
        return $differenceInMinutes <= 5;
    }

    private function checkCitaAlreadyWorked($cita_ids, $number_client) {
        $citaAlreadyCanceledOrAccepted = CitasModel::where('cita_ids', $cita_ids)
            ->where('number', $number_client)
            ->where(function($query) {
                $query->where('canceled', 1)
                      ->orWhere('accepted', 1);
            })
            ->exists();  
    
        return $citaAlreadyCanceledOrAccepted;
    }
    
 
}