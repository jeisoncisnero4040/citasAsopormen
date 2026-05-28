<?php

namespace App\Http\Controllers;

use App\Utils\ResponseManager;
use App\Services\CapacitacionMediaService;
use App\Dtos\CreateCapacitacionMediaDto;
use App\Dtos\GetCapacitacionMediaDto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Serializers\CapacitacionMediaSerializer;
use App\Models\CapacitacionMediaViewModel;
class CapacitacionMediaController extends Controller
{
    private CapacitacionMediaService $service;
    private ResponseManager $responseManager;

    public function __construct(
        CapacitacionMediaService $service,
        ResponseManager $responseManager
    ) {
        $this->service = $service;
        $this->responseManager = $responseManager;
    }

    public function store(Request $request): JsonResponse
    {
        $userRequesting = $request->user();
        $dto = CreateCapacitacionMediaDto::fromArray($request->all());



        return response()->json($this->responseManager->created([]),201);
    }

    public function index(Request $request): JsonResponse
    {
        $dto = new GetCapacitacionMediaDto();
        $results = $this->service->get($dto);
        $outputData = array_map(function(CapacitacionMediaViewModel $item){
            return CapacitacionMediaSerializer::serialize($item);
        }, $results);
        return response()->json($this->responseManager->success($outputData));
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