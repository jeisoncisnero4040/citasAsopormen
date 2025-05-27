<?php

namespace App\Http\Controllers;

use App\Services\DataAnlitycsPqrsService;
use Illuminate\Http\Request;

class DataAnalitycsController extends Controller
{
    private DataAnlitycsPqrsService $dataAnalitycsService;
    public function __construct(DataAnlitycsPqrsService $dataAnalitycsService)
    {
        $this->dataAnalitycsService=$dataAnalitycsService;
    }
    public function getDataFromPqrs(Request $request){
        $response=$this->dataAnalitycsService->getDataAnalisysPqrs($request->query());
        return response()->json($response,200);

    }
}
