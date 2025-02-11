<?php

namespace App\Http\Controllers;

use App\Services\CitasService;
use Illuminate\Http\Request;

class CitasController extends Controller
{   
    private $citasService;
    public function __construct(CitasService $citasService)
    {
        $this->citasService=$citasService;
    }
    public function sendCitaToWait(Request $request){
        $response=$this->citasService->sendCitaToWait($request);
        return response()->json($response);
    }
}
