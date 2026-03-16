<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditContoller extends Controller
{
    protected AuditService $auditService;
    public function __construct(AuditService $auditService) {
        $this->auditService=$auditService;
    }
    public function searchAudit($param, Request $request)
    {
        $response = $this->auditService->searchAudit($param, $request->query());
        return response()->json($response);
    }
    public function new(Request $request){
        $response = $this->auditService->createAudit($request->all());
        return response()->json($response,201);
    }
    
}
