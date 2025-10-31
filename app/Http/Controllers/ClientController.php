<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller {
    private ClientService $clientService;
    
    public function __construct(ClientService $clientService)
    {
        $this->clientService=$clientService;
    }

    public function searchClient(Request $request):JsonResponse{
        return response()->json(
            data:$this->clientService->searchClientByBame($request->query())
        );
    }

    public function find(string $code,Request $request):JsonResponse{
        return response()->json(
            data:$this->clientService->findClient(code:$code,request:$request->query())
        );
    }
    public function getInfoClient(Request $request):JsonResponse{
        return response()->json(
            data:$this->clientService->getClientInfoByCode(request:$request->query())
        );
    }
}