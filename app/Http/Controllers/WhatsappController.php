<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;

 
/**
 * @OA\Info(
 *     title="API ChatBot Asopormen",
 *     version="1.0.0",
 *     description="Documentación de la API del ChatBot Asopormen",
 *     @OA\Contact(
 *         email="sistemas@asopormen.org.co"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://swagger.local",
 *     description="Servidor de desarrollo"
 * )
 */

class WhatsappController extends Controller
{
    private $whatsappService;


    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;

    }
    /**
     * @OA\Post(
     *     path="/whatsapp/start_chat",
     *     tags={"WhatsApp"},
     *     summary="Enviar mensaje de confirmacion de citas al usuario ",
     *     description="Inicia un chat de WhatsApp para un cliente programando una cita con los datos proporcionados y envía un mensaje utilizando un servicio como Twilio.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                 "client", 
     *                 "profesional", 
     *                 "observations", 
     *                 "telephone_number", 
     *                 "date", 
     *                 "procedim", 
     *                 "direction", 
     *                 "session_ids"
     *             },
     *             @OA\Property(property="client", type="string", example="Juan Pérez"),
     *             @OA\Property(property="profesional", type="string", example="Dr. Andrés Gómez"),
     *             @OA\Property(property="observations", type="string", example="Paciente necesita consulta general"),
     *             @OA\Property(property="telephone_number", type="string", pattern="^3\d{9}$", example="3101234567"),
     *             @OA\Property(property="date", type="string", format="date-time", example="2024-11-25 14:30"),
     *             @OA\Property(property="procedim", type="string", example="Consulta médica"),
     *             @OA\Property(property="direction", type="string", example="Carrera 45 #23-15"),
     *             @OA\Property(property="session_ids", type="string", example="12345|||67890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={"message_id": "SM123456789", "status": "sent"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="Validation error: 'client' is required")
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

    public function startChat(Request $request){
        $response = $this->whatsappService->startChat($request->all());
        return response()->json($response, 200);
    }
    /**
     * @OA\Post(
     *     path="/whatsapp/failed",
     *     tags={"WhatsApp"},
     *     summary="Notificar error del sistema",
     *     description="Envía una notificación a la oficina de sistemas de Asopormen para alertar sobre un error en el sistema. Incluye detalles del número de teléfono asociado, la fecha del error y su origen.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"telephone_number", "date", "origin"},
     *             @OA\Property(
     *                 property="telephone_number", 
     *                 type="string", 
     *                 example="3001234567", 
     *                 description="Número de teléfono asociado al error. Debe ser un número móvil colombiano válido."
     *             ),
     *             @OA\Property(
     *                 property="date", 
     *                 type="string", 
     *                 format="date-time", 
     *                 example="2024-11-22T15:30:00Z"
     *                  
     *             ),
     *             @OA\Property(
     *                 property="origin", 
     *                 type="string", 
     *                 example="Fallo al conectar con la base de datos", 
     *                 description="Origen o módulo del sistema donde ocurrió el error."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación enviada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={"message_id": "SM123456789", "status": "sent"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="Validation error: 'telephone_number' is required")
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

    public function failedMessage(Request $request){
        $response = $this->whatsappService->sendFailedMessage($request->all());
        return response()->json($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/whatsapp/handle_message_client",
     *     tags={"WhatsApp"},
     *     summary="Procesar mensaje de cliente",
     *     description="Recibe un mensaje de WhatsApp de un cliente, identifica la intención del mensaje basado en su contenido y gestiona la cita asociada al número del cliente. Puede confirmar, cancelar o responder con mensajes predefinidos según el contexto.\n
     *                   la Url de este endpoint debe estar configurada como webhook en la consola de Twillio",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"From", "Body"},
     *             @OA\Property(property="From", type="string", example="whatsapp:+573101234567", description="Número de teléfono del cliente en formato internacional prefijado con `whatsapp:`"),
     *             @OA\Property(property="Body", type="string", example="confirmar cita", description="Texto del mensaje enviado por el cliente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={"message_id": "SM123456789", "status": "sent"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="Validation error: 'From' is required")
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

    public function handlemessageToClient(Request $request){
        $response=$this->whatsappService->handleClientMessage($request->all());
        return response()->json($response, 200);
    }

    /**
     * @OA\Post(
     *     path="/whatsapp/retrieve_password",
     *     tags={"WhatsApp"},
     *     summary="Enviar contraseña recuperada",
     *     description="Envía un mensaje a través de WhatsApp con la nueva contraseña de un usuario, utilizando su número de teléfono registrado.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"telephoneNumber", "password", "clientName"},
     *             @OA\Property(
     *                 property="telephoneNumber", 
     *                 type="string", 
     *                 example="3001234567", 
     *                 description="Número de teléfono del cliente en formato válido (por ejemplo, colombiano)."
     *             ),
     *             @OA\Property(
     *                 property="password", 
     *                 type="string", 
     *                 example="NuevaContraseña123", 
     *                 description="La nueva contraseña generada para el cliente."
     *             ),
     *             @OA\Property(
     *                 property="clientName", 
     *                 type="string", 
     *                 example="Juan Pérez", 
     *                 description="Nombre del cliente al que se le envía la nueva contraseña."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña enviada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", example={"message_id": "SM123456789", "status": "sent"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="bad request"),
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="error", type="string", example="Validation error: 'telephoneNumber' is required")
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
    public function SendMesageRetrievePassword(Request $request){
        $response=$this->whatsappService->sendMessageRetrievePassword($request->all());
        return response()->json($response, 200);
    }
    public function sendConfirmationOrderProgramedMessage(Request $request){
        $response=$this->whatsappService->sendConfirmationProgramedOrdenMessage($request->all());
        return response()->json($response, 200);
    }
    
    public function test()
    {
        // Define el nombre del Pokémon que quieres buscar
        $pokemonName = 'pikachu';
    
        // URL base de la API de Pokémon
        $url = "https://pokeapi.co:8081/api/v2/pokemon/pikachu";
    
        try {
            // Realiza la solicitud GET a la API
            $response = Http::get($url);
    
            // Verifica si la solicitud fue exitosa
            if ($response->successful()) {
                // Retorna la respuesta JSON de la API
                return response()->json($response->json(), 200);
            } else {
                // Maneja errores de la API
                return response()->json(['error' => 'No se pudo obtener información del Pokémon'], $response->status());
            }
        } catch (\Exception $e) {
            // Maneja excepciones en caso de fallos
            return response()->json(['error' => 'Ocurrió un error al comunicarse con la API', 'details' => $e->getMessage()], 500);
        }
    }

}