<?php

namespace App\Http\Controllers;

use App\Services\AuthsService;
use Illuminate\Http\Request;

class AuthsController extends Controller{
    private AuthsService $authsService;

    public function __construct(AuthsService $authsService) {
        $this->authsService = $authsService;
    }

    public function closeAuth(Request $request){
        $response= $this->authsService->closeAuth($request->all());
        return response()->json($response,200);
    }
}