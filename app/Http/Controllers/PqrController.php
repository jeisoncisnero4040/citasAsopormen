<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PqrService;
class PqrController extends Controller

{
    private PqrService $pqrService;
    public function __construct(PqrService $pqrService) {
        $this->pqrService=$pqrService;
    }
    public function new(Request $request){
        $response=$this->pqrService->createPqr($request->all());
        return response()->json($response,201);
    }
    public function find(int $pqr_id,Request $request){
        $response=$this->pqrService->findPqrById(pqrId:$pqr_id,request:$request->query());
        return response()->json($response,200);
    }
    public function getAll(Request $request){
        $response=$this->pqrService->getAllPqrs(request:$request->query());
        return response()->json($response,200);
    }
    public function notifyPqrsToArea(Request $pqrs){
        $token=$pqrs->bearerToken();
        $response=$this->pqrService->sendAreaNotification($pqrs->all(),$token??null);
        return response()->json($response,200);
    }
    public function handleAnswerPqrsByArea(Request $request){
        $data = $request->all();
        $actions = $data['actions'] ?? [];
        $files = $request->file('actions') ?? [];
        $result = $this->pqrService->saveAnswerArea($data, $actions, $files);
        return response()->json($result,200);
    }
    public function getActionsDataPqrs(int $pqr_id){
        $response=$this->pqrService->getCtionsDataPqrs($pqr_id);
        return response()->json($response,200);
    }
    public function getPqrsEncoded($pqrs_id_encoded){
        $response=$this->pqrService->getPqrsByIdHashed($pqrs_id_encoded);
        return response()->json($response,200);
    }
    public function changeArea($pqr_id,Request $newDataPqrsData){
        $response=$this->pqrService->changeAreaPqrs($pqr_id,$newDataPqrsData->all());
        return response()->json($response,200);
    }
    public function responsePqrsToClient(Request $request){
        $data = $request->all();
        $file = $request->file('file') ?? null;
        $adjuntos = $request->file('files_adjunt');
        $response=$this->pqrService->saveAnswerToUser($file,$data,$adjuntos);
        return response()->json($response,200);

    }
    public function closePqrs($pqrs_id){
        $response=$this->pqrService->closePqrs($pqrs_id);
        return response()->json($response,200);

    }
}
