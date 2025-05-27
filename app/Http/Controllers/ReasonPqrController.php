<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReasonsPqrService;

class ReasonPqrController extends Controller
{
    private ReasonsPqrService $reasonPqrService;
    public function __construct(ReasonsPqrService $reasonPqrService) {
        $this->reasonPqrService=$reasonPqrService;
    }

    public function get(){
        $response=$this->reasonPqrService->getAllReasonsPqrs();
        return response()->json($response,200);
    }
}
