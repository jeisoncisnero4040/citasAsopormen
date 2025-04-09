<?php

namespace App\Http\Controllers;

 

use Illuminate\Http\Request;
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
    /**
     * @OA\Post(
     *     path="/api/clients/request_password",
     *     summary="Generar nueva contraseña para un cliente y la envia por whatsapp o email",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para generar y enviar una nueva contraseña",
     *         @OA\JsonContent(
     *             required={"clientIdentity"},
     *             @OA\Property(
     *                 property="clientIdentity",
     *                 type="string",
     *                 example="123456",
     *                 description="Identificación del cliente (solo dígitos)"
     *             ),
     *             @OA\Property(
     *                 property="sendPasswordToEmail",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indica si se debe enviar la contraseña al correo electrónico del cliente"
     *             ),
     *             @OA\Property(
     *                 property="sendPasswordToMobile",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indica si se debe enviar la contraseña al número de teléfono del cliente"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nueva contraseña generada y enviada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="codigo", type="string", example="123456"),
     *                 @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="juan.perez@example.com"),
     *                 @OA\Property(property="thelephoneNumber", type="string", example="3012345678")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud o datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="El campo clientIdentity es obligatorio"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente no encontrado o no se pudo actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se han encontrado usuarios"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al generar la nueva contraseña"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function GenerateNewPasswordClient(Request $request){
        $password=$this->clientService->GnerateNewPasswordClient($request->all());
        return response()->json($password,200);
    }
    /**
     * @OA\Post(
     *     path="/api/clients/update_password",
     *     summary="Actualizar la contraseña de un cliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para actualizar la contraseña de un cliente",
     *         @OA\JsonContent(
     *             required={"clientIdentity", "password"},
     *             @OA\Property(
     *                 property="clientIdentity",
     *                 type="string",
     *                 example="123456",
     *                 description="Identificación del cliente (solo dígitos)"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="NuevaContraseña123",
     *                 description="Nueva contraseña (mínimo 6 caracteres)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=1, description="Número de clientes actualizados")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud o datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="El campo password es obligatorio"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Cliente no encontrado"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al actualizar la contraseña"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function UpdatePasswordClient(Request $request){
            $response=$this->clientService->setPasswordClient($request->all());
            return response()->json($response,200);
    }
    /**
     * @OA\Post(
     *     path="/api/clients/update",
     *     summary="Actualizar datos de un cliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos necesarios para actualizar un cliente",
     *         @OA\JsonContent(
     *             required={"clientCod"},
     *             @OA\Property(
     *                 property="clientCod",
     *                 type="string",
     *                 example="123456",
     *                 description="Código único del cliente"
     *             ),
     *             @OA\Property(
     *                 property="cel",
     *                 type="string",
     *                 example="3123456789",
     *                 description="Número de celular (Debe comenzar con 3 y tener 10 dígitos)",
     *                 nullable=true
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="cliente@example.com",
     *                 description="Correo electrónico válido",
     *                 nullable=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="integer", example=1, description="Número de clientes actualizados")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud o datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="El campo cel no es válido"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Cliente no encontrado"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al actualizar los datos del cliente"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function UpdateClient(Request $requets){
            $response=$this->clientService->updateClient($requets->all());
            return response()->json($response,200);
    }
    /**
     * @OA\Get(
     *     path="/api/clients/history_chat_bot/{codigo}",
     *     summary="Obtener historial de chat de un cliente con WhatsApp",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="codigo",
     *         in="path",
     *         required=true,
     *         description="Código único del cliente",
     *         @OA\Schema(type="string", example="123456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de chat obtenido con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-13T12:30:45Z"),
     *                     @OA\Property(property="message", type="string", example="Hola, ¿cómo puedo ayudarte?"),
     *                     @OA\Property(property="from", type="string", example="cliente"),
     *                     @OA\Property(property="to", type="string", example="bot")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Código de cliente no proporcionado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="No se ha proporcionado ni un cliente"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente sin número de celular registrado o sin historial de chat",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="failed"
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     example="El cliente no registra celular"
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     example="El usuario no guarda registro de chat"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="integer",
     *                     example=404
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="null",
     *                     example=null
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor al obtener el historial",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al obtener historial de chat"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function getHistoryClient($clientCode){
            $response = $this->clientService->getHistoryChatBotByClientCod($clientCode);
            return response()->json($response,200);
    }
    /**
     * @OA\Get(
     *     path="/api/clients/get_clients_by_cel/{celNumber}",
     *     summary="Obtener clientes por número de celular",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="celNumber",
     *         in="path",
     *         required=true,
     *         description="Número de celular asociado al cliente",
     *         @OA\Schema(type="string", example="3201234567")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Clientes encontrados con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="codigo", type="string", example="123456"),
     *                     @OA\Property(property="nombre", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="cedula", type="string", example="1001234567"),
     *                     @OA\Property(property="entidad", type="string", example="EPS Salud Total")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Clientes no encontrados con el número de celular proporcionado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Clientes no registrados"),
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor al buscar los clientes",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="error", type="string", example="Error al obtener clientes"),
     *             @OA\Property(property="status", type="integer", example=500),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */

    public function getClientsByNumber($celNumber){
            $response=$this->clientService->getClientsByNumberCel($celNumber);
            return response()->json($response,200);
    }

}