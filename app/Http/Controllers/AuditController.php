<?php


namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService=$auditService;
    }


    public function savePrintEvosAudit(Request $request): JsonResponse
    {
        return response()->json(
            data: $this->auditService->saveAuditPrintEvos(
                request: $request->all(),
                ip: $request->ip()
            ),
            status: 201
        );
}
}