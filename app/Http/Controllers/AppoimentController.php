<?php

namespace App\Http\Controllers;

use App\Dtos\ABAEvoDto;
use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Services\AppoimentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppoimentController extends Controller{
    private AppoimentService $appoimentService;

    public function __construct(AppoimentService $appoimentService)
    {
        $this->appoimentService=$appoimentService;
    }
    public function cancelAppoiment(Request $request){
        $response=$this->appoimentService->cancelAppoiments($request->all());
        return response()->json($response,200);
    }
    public function evoFono(Request $request){
        $evoDto =new BasicEvoDto($request);
        $response=$this->appoimentService->saveEvoFono($evoDto);
        return response()->json($response,200);
    }
    public function evoPsico(Request $request):JsonResponse{
        $evoDto =new PsicoEvoDto($request);
        $response=$this->appoimentService->evoPsico($evoDto);
        return response()->json($response,200);
    }
    public function evoABA(Request $request):JsonResponse{
        $evoDto =new ABAEvoDto($request);
        $response=$this->appoimentService->evoABA($evoDto);
        return response()->json($response,200);
    }
    public function getInfoDispoAppoById(Request $request):JsonResponse{
        $id=(int) $request->query('id');
        $response = $this->appoimentService->getDispoAppo(id:$id);
        return response()->json($response,200);
    }
    public function getHistAppoId(Request $request):JsonResponse{
        $id=(int) $request->query('id');
        $response = $this->appoimentService->getHistDxByAppoId(id:$id);
        return response()->json($response,200);
    }
    public function openPastAppos(Request $request){
        $response = $this->appoimentService->activePastAppos($request->all());
        return response()->json($response,200);
    }
    
}