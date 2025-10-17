<?php

namespace App\Http\Controllers;

use App\Dtos\UpdateProDto;
use App\Services\ProfesionalService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;


class ProfesionalController extends Controller{
    private ProfesionalService $profesionalService;

    public function __construct(ProfesionalService $profesionalService)

    {
        $this->profesionalService=$profesionalService;    
    }
    public function getDaylSchedule(Request $request){
        $response=$this->profesionalService->getDailyAppoiments($request->query());
        return response()->json($response,200);
    }
    public function getSchedule(Request $request){
        $response=$this->profesionalService->getSchedule($request->query());
        return response()->json($response,200);
    }
    public function getAuthsProfesional(Request $request){
        $response=$this->profesionalService->getProfesionalsAuth($request->query());
        return response()->json($response,200);
    }
    public function searchProfesionales(Request $request){
        $response=$this->profesionalService->searchProByName($request->query());
        return response()->json($response,200);
    }
    public function updateProfesional(Request $request)
    {
        $dto = new UpdateProDto($request->except('avatar'));
        $image = $request->file('avatar') ?? null;
        $result = $this->profesionalService->updateInfoPro(
            dto: $dto,
            image: $image
        );
        return response()->json(
            data: $result,
            status: 200
        );
    }
    
}