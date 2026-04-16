<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Dtos\GetAuditDto;
use App\utils\ResponseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    private AuditService $auditService;
    private ResponseManager $responseManager;

    public function __construct(AuditService $auditService, ResponseManager $responseManager)
    {
        $this->auditService = $auditService;
        $this->responseManager = $responseManager;
    }

    public function index(Request $request):JsonResponse{
        $dto = GetAuditDto::fromArray($request->query());
        $audits=$this->auditService->getAudits($dto);
        $audits = array_map(fn($audit) => $audit->toArray(), $audits);
        return response()->json(
            $this->responseManager->success($audits)
        );
    }
}