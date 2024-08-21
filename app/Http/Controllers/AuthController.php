<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\AuthService;
/**
 * @OA\Info(
 *     title="Api sistema de agendacion de citas asopormen",
 *     version="1.0",
 *     description="Api para la gestion de citas a usuarios de la ips Asopormen.sp",
 *     termsOfService="https://asopormen.org.co/",
 *     @OA\Contact(
 *         email="msolegario.cisneros@gmail.com",
 *         name="Equipo de soporte"
 *     ),
 *     @OA\License(
 *         name="Licencia",
 *         url="https://example.com/license"
 *     )
 * )
 *
 * @OA\Server(url="http://por_definir/api/documentation")
 */

 
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService=$authService;
    }
    
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Iniciar sesión",
     *     description="Autentica a un usuario utilizando su cédula y contraseña, y devuelve un token JWT junto con la información del usuario.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cedula", "password"},
     *             @OA\Property(property="cedula", type="string", example="1098707063"),
     *             @OA\Property(property="password", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="cedula", type="string", example="1098707063"),
     *                 @OA\Property(property="usuario", type="string", example="Sergio Avila"),
     *                 @OA\Property(property="estado", type="string", example="activo")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Credenciales incorrectas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="credenciales incorrectas")
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
    public function login(Request $request)
    {
        $dataLogin=$this->authService->login($request->all());
        return response() ->json($dataLogin,200)->header('authorization','Bearer '.$dataLogin['data']);
    }
    
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Cerrar sesión",
     *     description="Invalida el token JWT del usuario actual y realiza el logout.",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="Authorization", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="logout exitoso"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token no proporcionado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="logout exitoso"),
     *             @OA\Property(property="status", type="integer", example=200),
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
    public function logout(Request $request)
    {
        $response=$this->authService->logout($request);
        return response()->json($response,200);
    }
    
    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     tags={"Authentication"},
     *     summary="Actualizar token",
     *     description="Actualiza el token JWT del usuario actual.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Authorization"},
     *             @OA\Property(property="Authorization", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token actualizado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token no proporcionado o inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="token not found"),
     *             @OA\Property(property="status", type="integer", example=401),
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
    public function refresh(Request $request){
         $dataNewToken=$this->authService->refresh($request);
         return response()->json($dataNewToken,200)
         ->header('authorization','Bearer '.$dataNewToken['data']);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     tags={"Authentication"},
     *     summary="Obtener información del usuario actual",
     *     description="Devuelve los detalles del usuario autenticado usando el token JWT proporcionado en el encabezado `Authorization`.",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="Authorization", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario recuperada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="cedula", type="string", example="1098707063"),
     *                 @OA\Property(property="usuario", type="string", example="Sergio"),
     *                 @OA\Property(property="estado", type="string", example="active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token no proporcionado o inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="token not found"),
     *             @OA\Property(property="status", type="integer", example=401),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found"),
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
    public function me(Request $request)
    {
        $response = $this->authService->me($request);
        return response()->json($response, 200);
    }

}
