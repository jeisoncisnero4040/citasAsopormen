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
    public function searchAudit($param){
        $response=$this->auditService->searchAudit($param);
        return response()->json($response);
    }
}
