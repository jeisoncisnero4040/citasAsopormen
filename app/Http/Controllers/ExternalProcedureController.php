<?php

namespace App\Http\Controllers;

use App\Services\ExternalProceduresService;
use App\utils\ResponseManager;
use Illuminate\Http\Request;
use App\Dtos\GetExrenalProcedureDto;
use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExternalProcedureController extends Controller
{   
    private ExternalProceduresService $service;
    private ResponseManager $responseManager;
    public function __construct(
        ExternalProceduresService $service,
        ResponseManager $responseManager
    ){
        $this->service = $service;
        $this->responseManager = $responseManager;
    }

    public function index(Request $request):JsonResponse
    {

        $dto = GetExrenalProcedureDto::fromRequest($request->query());
        $procedures = $this->service->getProcedures($dto);
        $response=$this->responseManager->success($procedures);
        return response()->json(data:$response,status:200);
    }
}