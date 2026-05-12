<?php


namespace App\Http\Controllers;

use App\Dtos\GetAuthsDto;
use App\Services\AuthsService;
use App\utils\ResponseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Dtos\CreateAuthDto;
use App\Dtos\DeleteAuthsDto;
use App\Dtos\UpdateAuthsDto;



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

    public function store(Request $request):JsonResponse{
        $dto = CreateAuthDto::fromArray($request->all());
        $userRequesting =$this->user();
        $response = $this->service->Create($dto, $userRequesting);
        return response()->json(
            data:$this->responseManager->created($response),
            status:201
        );
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

    public function destroy(Request $request):JsonResponse{
        $dto=DeleteAuthsDto::fromArray($request->query());
        $userRequesting =$this->user();
        $this->service->deleteAuths(dto:$dto, userRequesting:$userRequesting);
        return response()->json(
            data:$this->responseManager->success([]),
            status:200
        );
    }
    public function update(Request $request):JsonResponse{
        $dto=UpdateAuthsDto::fromArray($request->all());
        $userRequesting =$this->user();
        $response = $this->service->updateAuths(dto:$dto, userRequesting:$userRequesting);
        return response()->json(
            data:$this->responseManager->success($response),
            status:200
        );
    }
}