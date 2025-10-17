<?php

namespace App\Http\Controllers;

use App\Dtos\EvoBufferDto;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BufferController extends Controller{
    private CacheService $bufferService;

    function __construct(CacheService $bufferService) {
        $this->bufferService=$bufferService;
    }
    function getAll(Request $request){
        return response()->json($this->bufferService->getAll(),200);

    }
    public function getEvo(Request $request):JsonResponse{
        $dtoEvoInBuffer=new EvoBufferDto($request);
        $data=$this->bufferService->getEvoInBuffer($dtoEvoInBuffer);
        return response()->json(data:$data,status:200);

    }

}