<?php

namespace App\Http\Controllers;
use App\Dtos\GetProfesionalSerderDto;
use App\utils\ResponseManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProfesionalSenderService ;
use App\Dtos\CreateProfesionalSender;

class ProfesionalSenderController extends Controller
{
    private ProfesionalSenderService $profesionalSenderService;
    private ResponseManager $responseManager;
    public function __construct(ProfesionalSenderService $profesionalSenderService, 
                                    ResponseManager $responseManager)
    {
        $this->profesionalSenderService = $profesionalSenderService;
        $this->responseManager = $responseManager;
    }

    public function index(Request $request)
    {
        $dto = GetProfesionalSerderDto::fromArray($request->query());
        $result = $this->profesionalSenderService->getProfesionalSenders($dto);
        return $this->responseManager-> success($result,200);
    }
    public function store(Request $request)
    {
        $dto = CreateProfesionalSender::fromArray($request->all());
        $user = $this->user();
        $result = $this->profesionalSenderService->create($dto,$user);
        return $this->responseManager-> success([]);
    }
    public function utility()
    {
        $result = $this->profesionalSenderService->utility();
        return $this->responseManager-> success($result);
    }
}