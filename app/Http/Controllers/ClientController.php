<?php

namespace App\Http\Controllers;

 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ClientService;

class ClientController extends Controller{

    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService=$clientService;
    }
    /**
     * @OA\Get(
     *     path="/api/get_clients/{string}",
     *     tags={"Clientes"},
     *     summary="Buscar clientes por nombre",
     *     description="Busca clientes cuyos nombres contengan la cadena proporcionada. Solo se devuelven los clientes activos y cuyo `ok_ent` es 0. Limita el número de resultados a 20.",
     *     @OA\Parameter(
     *         name="string",
     *         in="path",
     *         required=true,
     *         description="Cadena para buscar en el nombre de los clientes",
     *         @OA\Schema(
     *             type="string",
     *             example="John"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clientes encontrados exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="codigo", type="string", example="123"),
     *                     @OA\Property(property="nombre", type="string", example="John Doe")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetro de búsqueda no válido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="parametro de búsqueda debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Clientes no encontrados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="no se encontraron clientes"),
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
    public function getAllClientByStringSearch($string){
        $clients=$this->clientService->searchClientByString($string);
        return response()->json($clients,200);
    }

    /**
     * @OA\Post(
     *     path="/api/get_client",
     *     tags={"Clientes"},
     *     summary="Obtener información del cliente por historyId",
     *     description="Obtiene la información de un cliente usando un `historyId` numérico proporcionado en la solicitud. La respuesta incluye detalles del cliente como nombre, nit, fecha de nacimiento, entre otros.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="historyId", type="string", example=123456, description="ID histórico del cliente. solo caracteres numericos."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información del cliente obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="codigo", type="string", example="12345"),
     *                     @OA\Property(property="nombre", type="string", example="John Doe"),
     *                     @OA\Property(property="nit_cli", type="string", example="900123456"),
     *                     @OA\Property(property="f_nacio", type="string", format="date", example="1990-01-01"),
     *                     @OA\Property(property="sexo", type="string", example="M"),
     *                     @OA\Property(property="direcc", type="string", example="123 Main St"),
     *                     @OA\Property(property="barrio", type="string", example="Downtown"),
     *                     @OA\Property(property="cel", type="string", example="555-1234"),
     *                     @OA\Property(property="municipio", type="string", example="CityName"),
     *                     @OA\Property(property="entidad", type="string", example="EntityName")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Parámetro `historyId` inválido",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="parametro historyId debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Información del cliente no encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="informacion de usuario no encontrada"),
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
    public function showDataClientByIdHistory(Request $request)
    {
        $infoClient=$this->clientService->getDataClientByHistoryId($request->all());
        return response()->json($infoClient,200);
    }

    /**
     * @OA\Get(
     *     path="/clients/get_authorizations/{clientCode}",
     *     tags={"Clientes"},
     *     summary="Obtener autorizaciones por código de cliente",
     *     description="Obtiene las primeras 20 autorizaciones activas de un cliente específico, excluyendo aquellas anuladas o suspendidas.",
     *     @OA\Parameter(
     *         name="clientCode",
     *         in="path",
     *         description="Código del cliente para buscar sus autorizaciones",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="123456"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autorizaciones encontradas exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="n_autoriza", type="string", example="AUT123"),
     *                 @OA\Property(property="fecha", type="string", format="date", example="2024-09-10"),
     *                 @OA\Property(property="f_vence", type="string", format="date", example="2025-09-10"),
     *                 @OA\Property(property="codent", type="string", example="ENT001"),
     *                 @OA\Property(property="codent2", type="string", example="PAQ001"),
     *                 @OA\Property(property="observa", type="string", example="Observaciones adicionales")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El código de cliente es inválido o está vacío",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="el parametro de búsqueda debe ser válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="El cliente no registra autorizaciones",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="El usuario no registra autorizaciones"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function getAuthorizationByClientCode($clientCode){
        $authorizations=$this->clientService->getAuthorizationsByCliencode($clientCode);
        return response()->json($authorizations,200);

    }
    /**
     * @OA\Get(
     *     path="/clients/get_authorization_data/{authorizationCode}",
     *     tags={"Clientes"},
     *     summary="Obtener datos de autorización por código",
     *     description="Obtiene los detalles de una autorización específica usando el código de autorización.",
     *     @OA\Parameter(
     *         name="authorizationCode",
     *         in="path",
     *         description="Código de la autorización",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="AUTH123456"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos de la autorización obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="n_autoriza", type="string", example="AUTH123456"),
     *                 @OA\Property(property="tiempo", type="string", example="30"),
     *                 @OA\Property(property="procedim", type="string", example="Consulta médica"),
     *                 @OA\Property(property="cantidad", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El código de autorización es inválido o está vacío",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="el codigo de authorization debe ser un codigo valido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function getDataFromAuthorization($authorizationCode){
        $dataAuthorization=$this->clientService->getDataFromAuthorizationCode($authorizationCode);
        return response()->json($dataAuthorization,200);
    }
   
   
    


    
    
}