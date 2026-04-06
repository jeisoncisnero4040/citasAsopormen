<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\BaseModel;
use App\Models\InformesModel;
use App\Requests\InformesRequest;
use App\utils\ResponseManager;
use Carbon\Carbon;

class InformesService extends BaseModel{
   
    private ResponseManager $responseManager;
    private InformesModel $informesRepository;

    public function __construct(ResponseManager $responseManager, InformesModel $informesRepository)
    {
        $this->responseManager=$responseManager;
        $this->informesRepository=$informesRepository;

    }
    public function getInformeNewClients($request){
        InformesRequest::validateUserNewsData($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $procedure=$request['procedure'];
        $data=$this->informesRepository->getUserWhithNewProcedure($from,$to,$procedure);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);

    }
    public function getInformeUserNotAppoiments($request){
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d H:m:s");
        $to=Carbon::parse($request['to'])->format("Y-m-d H:m:s");
        $data=$this->informesRepository->getUsersWithOutAppoiments($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);

    }
    public function getAppoimentsByDayByEntity($request){
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $data=$this->informesRepository->countCitasByEntity($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }

    public function getNewClientsByProcedure($request){
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $data=$this->informesRepository->getNewClientsByProcedure($from,$to);
        return $this->responseManager->success($data);
    }
    public function getoldUsersInService($request){
        InformesRequest::validateUserNewsData($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $procedure=$request['procedure'];
        $data=$this->informesRepository->getOldUserInProcedipro($from,$to,$procedure);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }
    public function oldUsersNotCitas($request){
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d H:m:s");
        $to=Carbon::parse($request['to'])->format("Y-m-d H:m:s");
        $data=$this->informesRepository->getOldUsersWithoutFutureAppoiments($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }

}