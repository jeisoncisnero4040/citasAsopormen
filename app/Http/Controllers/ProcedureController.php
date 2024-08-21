<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProcedureService;

class ProcedureController extends Controller
{
    private $procedureService;

    public function __construct(ProcedureService $procedureService)
    {
        $this->procedureService=$procedureService;
    }
    public function getAllProcedures(){
        $procedures=$this->procedureService->getAllProcedures();
        return response()->json($procedures,200);
    }
}
