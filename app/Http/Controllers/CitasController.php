<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CitasService;
use PHPUnit\Framework\Attributes\IgnoreFunctionForCodeCoverage;

class CitasController extends Controller
{
    private $citasService;
    
    public function __construct(CitasService $citasService)
    {
        $this->citasService=$citasService;
    }

    public function createGroupCitas(Request $request){
        $citas=$this->citasService->createGroupCitas($request->all());
        return response()->json($citas,201);
    }
    public function GetNumCitasFromOrder($authorization,$procedim){
        $numCitas=$this->citasService->GetNumCitasFromOrder($authorization,$procedim);
        return response()->json($numCitas,200);
    }
    public function GetCalendarClient(Request $request){
        $calendarClient=$this->citasService->getCitasByClientInRangeTime($request->all());
        return response()->json($calendarClient,200);
    }
    public function deleteCitaById($id){
        $citaDelete=$this->citasService->deleteCitaById(($id));
        return response()->json($citaDelete,200);
    }
    public function deleteAllCitasByProfesionalDay(Request $request){
        $citasDleted=$this->citasService->deleteDayCitasProfesional($request->all());
        return response()->json($citasDleted,200);
    }
}
