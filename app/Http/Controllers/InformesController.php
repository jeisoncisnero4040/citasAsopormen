<?php

namespace App\Http\Controllers;

use App\Services\InformesService;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    private InformesService $informesService;

    public function __construct(InformesService $informesService)
    {
        $this->informesService=$informesService;
    }
    public function getNewClientsInforme(Request $request){
        $user = $this->user();
        $response = $this->informesService->getInformeNewClients($request->query(), $user);
        return response()->json($response,200);
    }
    public function getClientsAppoimentsNotFound(Request $request){
        $user = $this->user();
        $response = $this->informesService->getInformeUserNotAppoiments($request->query(), $user);
        return response()->json($response,200);
    }
    public function countAppimentsEntity(Request $request){
        $user = $this->user();
        $response = $this->informesService->getAppoimentsByDayByEntity($request->query(), $user);
        return response()->json($response,200);
    }

    public function countNewsClientsByProcedure(Request $request){
        $user = $this->user();
        $response = $this->informesService->getNewClientsByProcedure($request->query(), $user);
        return response()->json($response,200);
    }
    public function getOldUser(Request $request){
        $user = $this->user();
        $response = $this->informesService->getoldUsersInService($request->query(), $user);
        return response()->json($response,200);
    }
    public function getOldUserNotFountCitad(Request $request){
        $user = $this->user();
        $response = $this->informesService->oldUsersNotCitas($request->query(), $user);
        return response()->json($response,200);

    }
    public function getAuthsAdded(Request $request){
        $user = $this->user();
        $response = $this->informesService->getAuthsAdded($request->query(), $user);
        return response()->json($response,200);
    }
    
}
