<?php

namespace App\Http\Controllers;

use App\Services\OrdersCaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CaseOrdersController extends Controller
{
    private $ordersCaseService;

    public function __construct(OrdersCaseService $ordersCaseService)
    {
        $this->ordersCaseService = $ordersCaseService;
    }

    /**
     * Crea un nuevo caso de orden.
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->all();
        $response = $this->ordersCaseService->newCase($data);
        return response()->json($response);
    }

    /**
     * Obtiene todos los casos no finalizados.
     */
    public function getAllCasosAvaiables(): JsonResponse
    {
        $response = $this->ordersCaseService->getAllCasosAvaiables();
        return response()->json($response);
    }

    /**
     * Obtiene un caso por ID.
     */
    public function getById(int $id): JsonResponse
    {
        $response = $this->ordersCaseService->getCaseById($id);
        return response()->json($response);
    }

    /**
     * Acepta un caso.
     */
    public function acceptCase(Request $request): JsonResponse
    {
        $data = $request->all();
        $response = $this->ordersCaseService->acceptCase($data);
        return response()->json($response);
    }

    /**
     * Rechaza un caso.
     */
    public function rejectCase(Request $request): JsonResponse
    {
        $data = $request->all();
        $response = $this->ordersCaseService->rejectCase($data);
        return response()->json($response);
    }

    /**
     * Cierra un caso.
     */
    public function closeCase(Request $request): JsonResponse
    {
        $data = $request->all();
        $response = $this->ordersCaseService->closedCase($data);
        return response()->json($response);
    }
    public function searchCitasClient(Request $request):JsonResponse{
        
        $data = $request->all();
        $response = $this->ordersCaseService->searchCasesByClient($data);
        return response()->json($response);
    }
}