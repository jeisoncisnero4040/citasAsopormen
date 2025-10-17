<?php

namespace App\Http\Controllers;

use App\Dtos\GetEvoDto;
use App\Services\EvoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvoController extends Controller{
    private EvoService $evoService;

    public function __construct(EvoService $evoService){
        $this->evoService=$evoService;
    }
    public function getProcediproToPrint(Request $request):JsonResponse
    {
        return response()->json(
                            data:$this->evoService->getProcediprosToPrintEvo($request->query())
                        );
    }

    public function getEvo(Request $request){
        $data=$request->all();
        return response()
                ->json(
                    data:$this->evoService->getEvo(new GetEvoDto(data:$data))
                );
    }
}