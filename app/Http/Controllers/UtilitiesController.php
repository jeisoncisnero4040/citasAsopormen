<?php

namespace App\Http\Controllers;

use App\Services\UtilitiesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilitiesController extends Controller
{
    private UtilitiesService $utilitiesService;

    public function __construct(UtilitiesService $utilitiesService)
    {
        $this->utilitiesService = $utilitiesService;
    }

    public function getEvoUtilities(): JsonResponse
    {
        $response= $this->utilitiesService->getEvoUtilities();
        return response()->json($response,200);
    }
    public function searchDx(Request $request): JsonResponse
    {
        $response= $this->utilitiesService->searchDx($request->query());
        return response()->json($response,200);
    }
}
