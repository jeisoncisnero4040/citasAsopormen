<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
/**
 * @OA\Info(
 *     title="API de PQRS",
 *     version="1.0.0",
 *     description="Documentación de la API para gestionar PQRS (Peticiones, Quejas, Reclamos y Sugerencias).",
 *     @OA\Contact(
 *         email="soporte@tusitio.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $response = $this->authService->login($request->only(['cedula', 'password']));
        return response()->json($response, 200);
    }
    public function setPasswordUser(Request $request){
        $response = $this->authService->updatePasswordByUserCedula($request->all());
        return response()->json($response, 200);
    }

    public function recoverPassword(Request $request){
    $response = $this->authService->recoverPassword($request->all());
    return response()->json($response, 200);
    }
}
