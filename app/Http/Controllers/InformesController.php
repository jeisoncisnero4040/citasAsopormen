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
        $response = $this->informesService->getInformeNewClients($request->query());
        return response()->json($response,200);
    }
    public function getClientsAppoimentsNotFound(Request $request){
        $response = $this->informesService->getInformeUserNotAppoiments($request->query());
        return response()->json($response,200);
    }
    public function countAppimentsEntity(Request $request){
        $response = $this->informesService->getAppoimentsByDayByEntity($request->query());
        return response()->json($response,200);
    }

    public function countNewsClientsByProcedure(Request $request){
        $response = $this->informesService->getNewClientsByProcedure($request->query());
        return response()->json($response,200);
    }
    public function getOldUser(Request $request){
        $response = $this->informesService->getoldUsersInService($request->query());
        return response()->json($response,200);
    }
    public function getOldUserNotFountCitad(Request $request){
        $response = $this->informesService->oldUsersNotCitas($request->query());
        return response()->json($response,200);

    }
    
}
