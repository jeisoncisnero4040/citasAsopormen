<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UtilsPqrService;

class UtilsPqrControllers extends Controller
{
    private UtilsPqrService $utilsPqrService;

    public function __construct(UtilsPqrService $utilsPqrService) {
        $this->utilsPqrService=$utilsPqrService;
    }
    public function get(){
        $response=$this->utilsPqrService->getUtility();
        return response()->json($response,200);
    }
}
