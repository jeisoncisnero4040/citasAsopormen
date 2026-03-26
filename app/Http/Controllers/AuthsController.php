<?php


namespace App\Http\Controllers;

use App\Dtos\GetAuthsDto;
use App\Services\AuthService;
use App\Services\AuthsService;
use App\utils\ResponseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthsController extends Controller{
    private AuthsService $service;
    private ResponseManager $responseManager;

    public function __construct(
        AuthsService $service,
        ResponseManager $responseManager
    )
    {
        $this->service=$service;
        $this->responseManager =$responseManager;
    }

    public function stores():void{
        return;
    }
    public function index(Request $request):JsonResponse{
        $dto=GetAuthsDto::fromArray($request->query());
        $response = $this->service->get(dto:$dto);
        return response()->json(
            data:$this->responseManager->success($response),
            status:200
        );
    }
    public function getDetail(Request $request):JsonResponse{
        $dto=GetAuthsDto::fromArray($request->query());
        $response = $this->service->getAuthDetail(dto:$dto);
        return response()->json(
            data:$this->responseManager->success($response),
            status:200
        ); 
    }
}