<?php

namespace App\Http\Controllers;

use App\Utils\ResponseManager;
use App\Services\AuthsDocumentsService;
use App\Dtos\CreateAuthsDocumentsDto;
use App\Dtos\GetAuthsDocumentsDto;
use App\Models\AuthsDocumentsViewModel;
use App\Serializers\AuthsDocumentsSerializer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthsDocumentsController extends Controller
{
    private AuthsDocumentsService $service;
    private ResponseManager $responseManager;

    public function __construct(
        AuthsDocumentsService $service,
        ResponseManager $responseManager
    ) {
        $this->service = $service;
        $this->responseManager = $responseManager;
    }

    public function store(Request $request): JsonResponse
    {
        $file = $request->file('document');
        $userRequesting = $this->user();
        $dto = CreateAuthsDocumentsDto::fromArray($request->all());

        $documents = $this->service->create($dto, $userRequesting, $file);
        $response = array_map(
            function (AuthsDocumentsViewModel $item) {return AuthsDocumentsSerializer::serialize($item);},
            $documents
        );
        return response()->json($this->responseManager->created($response),201);
    }

    public function index(Request $request): JsonResponse
    {
        $dto = GetAuthsDocumentsDto::fromArray($request->query());
        $documents = $this->service->get($dto);
        $response = array_map(
            function (AuthsDocumentsViewModel $item) {return AuthsDocumentsSerializer::serialize($item);},
            $documents
        );
        return response()->json($this->responseManager->success($response));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        //logica de remplazar archivo, eliminar el anterior y guardar el nuevo, actualizar la base de datos con la nueva informacion del archivo
        return response()->json($this->responseManager->success([]), 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $userRequesting = $this->user();
        $this->service->delete($id, $userRequesting);
        return response()->json($this->responseManager->success([]), 200);
    }
    public function getUtility():JsonResponse{
        $response = $this->service->getUtility();
        return response()->json(
            data:$this->responseManager->success($response),
            status:200
        );
    }
}