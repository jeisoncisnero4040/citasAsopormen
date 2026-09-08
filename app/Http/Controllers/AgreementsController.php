<?php

namespace App\Http\Controllers;

use App\Utils\ResponseManager;
use App\Services\AgreementsService;
use App\Dtos\CreateAgreementsDto;
use App\Dtos\GetAgreementsDto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Serializers\AgreementsSerializer;

class AgreementsController extends Controller
{
    private AgreementsService $service;
    private ResponseManager $responseManager;

    public function __construct(
        AgreementsService $service,
        ResponseManager $responseManager
    ) {
        $this->service = $service;
        $this->responseManager = $responseManager;
    }

    public function store(Request $request): JsonResponse
    {
        $userRequesting = $request->user();
        $dto = CreateAgreementsDto::fromArray($request->all());



        return response()->json($this->responseManager->created([]),201);
    }

    public function index(Request $request): JsonResponse
    {
        $dto = GetAgreementsDto::fromArray($request->all());
        $agreements = $this->service->get($dto);
        $output = array_map(fn($agreement) => AgreementsSerializer::serialize($agreement), $agreements);
        return response()->json($this->responseManager->success($output));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return response()->json($this->responseManager->success([]));
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json($this->responseManager->success([]));
    }
}