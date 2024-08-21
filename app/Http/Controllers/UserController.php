<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use GuzzleHttp;
use GuzzleHttp\Client;

class UserController extends Controller
{
    protected $userService;

    public function __construct(userService $userService)
    {
        $this->userService=$userService;
    }
   
    /**
     * @OA\Post(
     *     path="/api/recover_password",
     *     tags={"Usuario"},
     *     summary="Recuperar contraseña de usuario",
     *     description="Permite a un usuario recuperar su contraseña proporcionando su cédula y un email. Se enviará un correo de recuperación con la nueva contraseña.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cedula", "email"},
     *             @OA\Property(property="cedula", type="string", example="1098707063", description="La cédula del usuario. Solo números."),
     *             @OA\Property(property="email", type="string", example="usuario@example.com", description="El email del usuario para enviar la nueva contraseña.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña recuperada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Parámetros de solicitud inválidos"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado o email no coincide",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado o email no coincide"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function recoverPassword(Request $request){

        $isPasswordUpdated=$this->userService->recoverPassword($request->all());
        return response()->json($isPasswordUpdated,200);
    
    }

    /**
     * @OA\Post(
     *     path="/api/update_password",
     *     tags={"Usuario"},
     *     summary="Actualizar contraseña de usuario",
     *     description="Permite a un usuario actualizar su contraseña proporcionando la cédula, la contraseña actual y la nueva contraseña.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cedula", "old_password", "new_password"},
     *             @OA\Property(property="cedula", type="string", example="1098707063", description="La cédula del usuario. Solo números."),
     *             @OA\Property(property="old_password", type="string", example="OldPassword123", description="La contraseña actual del usuario."),
     *             @OA\Property(property="new_password", type="string", example="NewPassword123", description="La nueva contraseña del usuario.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña actualizada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Contraseña actualizada exitosamente"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida o datos incorrectos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Solicitud inválida o datos incorrectos"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado o contraseña actual incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado o contraseña actual incorrecta"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Error interno del servidor"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function updatePasswordByUserCedula(Request $request){
        $isPasswordUpdated=$this->userService->updatePasswordByUserCedula($request->all());
        return response()->json($isPasswordUpdated,200);
    }

    //public function f($cp){
        //$client=new Client();
        //$token='76661443-cb8e-45e6-9a88-e8e1f289f857';
        //$response = $client->request('GET',
           // 'https://api.copomex.com/query/get_colonia_por_cp/'.$cp.'?token='.$token,
        //);
        //// return response()->json($responseData);


    //}
}