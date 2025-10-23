<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $response = $this->authService->login($request->all(),$request->ip());

        return response()
            ->json($response, 200)
            ->header('Authorization', 'Bearer ' . $response['data']['token']);
    }

    public function logout(Request $request): JsonResponse
    {
        $response = $this->authService->logout($request);

        return response()->json($response, 200);
    }

    public function refresh(Request $request): JsonResponse
    {
        $newToken = $this->authService->refresh($request);

        return response()
            ->json($request, 200)
            ->header('Authorization', 'Bearer ' . $newToken);
    }

    public function me(Request $request): JsonResponse
    {
        $response = $this->authService->me($request);
        return response()->json($response, 200);
    } 
    public function forgotPassword(Request $request){
        $response = $this->authService->forgotPassword($request->all());
        return response()->json($response, 200);
    }
    public function changePassword(Request $request){

        $response = $this->authService->changePassword($request->all());
        
        return response()->json($response, 200);
    }
            
    public function defaultPass(Request $request)
    {
        $response = $this->authService->defaultPasswords($request->all());
        return response()->json($response);
    }
}
