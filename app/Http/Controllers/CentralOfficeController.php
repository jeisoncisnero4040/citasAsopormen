<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CentralOfficeService;

class CentralOfficeController extends Controller
{
    private $centralOfficeService;

    public function __construct(CentralOfficeService $centralOfficeService){
        $this->centralOfficeService=$centralOfficeService;
    }
     public function getCentralsOffice(){
        $officesResponse=$this->centralOfficeService->getAllCentralsOffice();
        return response()->json($officesResponse,200);
     }
}
