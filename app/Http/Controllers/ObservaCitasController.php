<?php

namespace App\Http\Controllers;

use App\Services\ObservaCitasService;
use Illuminate\Http\Request;

class ObservaCitasController extends Controller
{
    private $observaCitasService;

    public function __construct(ObservaCitasService $observaCitasService)
    {
        $this->observaCitasService = $observaCitasService;
    }

     
    public function getAllObservaCitas()
    {
        $observaCitas = $this->observaCitasService->getObservationName();
        return response()->json($observaCitas, 200);
    }

     
    public function getContentObservation($name)
    {
        $observation = $this->observaCitasService->getObservationContentById($name);
        return response()->json($observation, 200);
    }
}
