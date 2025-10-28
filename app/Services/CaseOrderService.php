<?php
namespace App\Services;

use App\Utils\ResponseManager;
use App\Services\ChatsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class CaseOrderService{
    private $responseManager;
    private $chatsService;
    const URLCITASSERVICE = 'https://citas.asopormen.co:8081/';

    public function __construct(ResponseManager $responseManager, ChatsService $chatsService) {
        $this->responseManager = $responseManager;
        $this->chatsService = $chatsService;
    }

    public function sendChatDataToOrderCase($numcel){
        $chat = $this->getDataToChat($numcel);
        $infoUser = $chat['data_user'] ?? [];
        $infoOrder = $chat['order_data'] ?? []; 
        $userData = $this->getInfoUserToSave($infoUser, $numcel);
        $orderData = $this->getOrderData($infoOrder);
        $payload = $this->builPayload($userData, $orderData);
        $url = self::URLCITASSERVICE . "api/case/new";
        return $this->sendRequestToApi($url, $payload,$method='post');
    }
    public function getOrdersUser($numcel){
        $chat = $this->getDataToChat($numcel);
        $infoUser = $chat['data_user'] ?? [];
        $payload=$this->builPayloadToGetOrdersUser($infoUser,$numcel);
        $url = self::URLCITASSERVICE . "api/case/search";
        return $this->sendRequestToApi($url, $payload,$method='post');
    }
    public function builPayloadToGetOrdersUser($dataUser,$numCel){
        return [
            'codigo'=> $dataUser['codigo'] ?? null,
            'cedula'=>$dataUser['cedula'],
            'celular'=>$numCel
        ];
    }

    public function sendRequestToApi(string $url, array $payload, string $method = 'get') {
        try {
            $response = $this->makeHttpRequest($url, $payload, $method);
            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            Log::error("❌ Excepción capturada: " . $e->getMessage());
            return $this->responseManager->serverError("Error inesperado al procesar la solicitud.");
        }
    }
    
    private function makeHttpRequest(string $url, array $payload, string $method) {
        switch (strtolower($method)) {
            case 'get':
                return Http::get($url, $payload);
            case 'post':
                return Http::post($url, $payload);
            default:
                throw new \InvalidArgumentException("Método HTTP no soportado: $method");
        }
    }
    
    private function handleApiResponse($response) {
        if ($response->status() === 404) {
            Log::warning("⚠️ Recurso no encontrado: " . $response->body());
            return $this->responseManager->notFound("Recurso no encontrado.");
        }
    
        if (!$response->successful()) {
            Log::error("❌ Error HTTP: " . $response->status() . " - " . $response->body());
            return $this->responseManager->serverError("Error en la solicitud.");
        }
    
        $data = $response->json();
    
        if (empty($data['data']) || !is_array($data['data'])) {
            Log::error("❌ Formato inválido en la respuesta: " . json_encode($data));
            return $this->responseManager->serverError("Formato inválido recibido de la API.");
        }
    
        return $this->responseManager->success($data['data']);
    }
    
    

    private function getDataToChat($numcel){
        
        $response = $this->chatsService->getChatByNumCel($numcel);
        return $response['data'] ?? [];
    }

    private function getInfoUserToSave($infoUser, $numCel){
        return [
            'num_historia' => $infoUser['codigo'] ?? null,
            'cedula_cliente' => $infoUser['cedula'],
            'nombre_cliente' => $infoUser['nombre'],
            'celular_cliente' => $numCel,
            'eps_cliente' => $infoUser['entidad'],
            'direccion_cliente' => $infoUser['direccion'] ?? null,
            'email_cliente' => $infoUser['email'] ?? null,
        ];
    }

    private function getOrderData($orderData){
        return [
            'url_imagen_cedula1' => $orderData['front_cedula'][0] ?? null,
            'url_imagen_cedula2' => $orderData['back_cedula'][0] ?? null,
            'url_imagen_order' => $orderData['order'][0] ?? null,
            'url_imagen_historia_1' => $orderData['clinical_history'][0] ?? null,
            'url_imagen_historia_2' => $orderData['clinical_history'][1] ?? null,
            'url_imagen_historia_3' => $orderData['clinical_history'][2] ?? null,
            'url_imagen_historia_4' => $orderData['clinical_history'][3] ?? null,
            'url_imagen_historia_5' => $orderData['clinical_history'][4] ?? null,
            'url_imagen_preautoriz' => $orderData['preautoriz'][0] ?? null,
            'url_imagen_autoriz' => $orderData['autoriz'][0] ?? null,
            'url_case_in_pdf' => $orderData['pdf'][0] ?? null,
            'descripcion_caso_particular' => $orderData['details_particular_user'] ?? null,
            'observaciones_caso' => $orderData['observations'] ?? null,
            'codigo_autorizacion' => $orderData['autoriz_code'] ?? null
        ];
    }

    private function builPayload(array $infoUser, array $infoOrder){
        return array_merge($infoUser, $infoOrder);
    }
}
