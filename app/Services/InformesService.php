<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\BaseModel;
use App\Models\InformesModel;
use App\Requests\InformesRequest;
use App\utils\ResponseManager;
use Carbon\Carbon;
use App\Domain\Date;
use App\Models\UserRequesting;
use App\Exceptions\CustomExceptions\ForbidenException;
class InformesService extends BaseModel{
   
    private ResponseManager $responseManager;
    private InformesModel $informesRepository;

    public function __construct(ResponseManager $responseManager, InformesModel $informesRepository)
    {
        $this->responseManager=$responseManager;
        $this->informesRepository=$informesRepository;

    }
    public function getInformeNewClients(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
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
    public function getInformeUserNotAppoiments(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d H:m:s");
        $to=Carbon::parse($request['to'])->format("Y-m-d H:m:s");
        $data=$this->informesRepository->getUsersWithOutAppoiments($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);

    }
    public function getAppoimentsByDayByEntity(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $data=$this->informesRepository->countCitasByEntity($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }

    public function getNewClientsByProcedure(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d");
        $to=Carbon::parse($request['to'])->format("Y-m-d");
        $data=$this->informesRepository->getNewClientsByProcedure($from,$to);
        return $this->responseManager->success($data);
    }
    public function getoldUsersInService(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
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
    public function oldUsersNotCitas(array $request, UserRequesting $user){
        $this->validateLeaderAppoiments($user);
        InformesRequest::validateDateRange($request);
        $from =Carbon::parse($request['from'])->format("Y-m-d H:m:s");
        $to=Carbon::parse($request['to'])->format("Y-m-d H:m:s");
        $data=$this->informesRepository->getOldUsersWithoutFutureAppoiments($from,$to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }
    public function getAuthsAdded(array $request, UserRequesting $user){
        if(!$user->isAdmisionUser()){
            throw new ForbidenException("El usuario no tiene permisos para acceder a este informe",403);
        }
        InformesRequest::validateDateRange($request);
        $from =Date::create($request['from']);
        $to=Date::create($request['to'])->plusDays();
        $data=$this->informesRepository->getAuthsAdded(from: $from, to: $to);
        if(empty($data)){
            throw new NotFoundException("No se han encontrado reagistros en este rango de Tiempo",404);
        }
        return $this->responseManager->success($data);
    }
    public function validateLeaderAppoiments(UserRequesting $user):void{
        if(!$user->isAppoimentLeader()){
            throw new ForbidenException("El usuario no tiene permisos para acceder a este informe",403);
        }
    }
}