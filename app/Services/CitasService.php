<?php
namespace App\Services;

use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Http;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\NotFoundException;
use Illuminate\Support\Facades\Log;
class CitasService {
    private $responseManager;
    const URLCITASSERVICE = 'https://citas.asopormen.co:8081';

    public function __construct(ResponseManager $responseManager) {
        $this->responseManager = $responseManager;
    }

    public function getClientsByNumberCel($celNumber) {
        $url = self::URLCITASSERVICE . "/api/clients/get_clients_by_cel/$celNumber";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $this->responseManager->success($data['data']);
            } else {
                return $this->responseManager->serverError("Error en la respuesta del servidor.");
            }
        } catch (\Exception $e) {
            return $this->responseManager->serverError("Error al hacer la solicitud: " . $e->getMessage());
        }
    }
    public function getCitasClient($clientCode)
    {   
        if(!$clientCode){
            return $this->responseManager->notFound("Citas no encontradas.");
        }
        $url = self::URLCITASSERVICE . "/api/citas/get_citas_client/$clientCode";
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                $citas = $data['data'];

                if (empty($citas)) {
                    return $this->responseManager->notFound("Citas no encontradas.");
                }

                if (is_array($citas) && count($citas) > 0 && is_array($citas[0])) {
                    $citasFlated = array_merge(...$citas);
                } else {
                    $citasFlated = $citas; 
                }
                return $this->responseManager->success($citasFlated);
            }

            return $this->responseManager->serverError(
                "Error en la respuesta del servidor:" . $response->status()
            );
    
        } catch (\Throwable $e) {  
            return $this->responseManager->serverError(
                "Error al hacer la solicitud: " . $e->getMessage()
            );
        }
    }
    
    public function getHistoryClient($clientCode)
    {
        $url = self::URLCITASSERVICE . "/api/citas/get_citas_client_history/$clientCode";
    
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                $citas = $data['data'];

                if (empty($citas)) {
                    return $this->responseManager->notFound("Citas no encontradas.");
                }

                if (is_array($citas) && count($citas) > 0 && is_array($citas[0])) {
                    $citasFlated = array_merge(...$citas);
                } else {
                    $citasFlated = $citas; 
                }
                return $this->responseManager->success($citasFlated);
            }

            return $this->responseManager->serverError(
                "Error en la respuesta del servidor:" . $response->status()
            );
    
        } catch (\Throwable $e) {  
            return $this->responseManager->serverError(
                "Error al hacer la solicitud: " . $e->getMessage()
            );
        }
    }
    public function cancelCita(array $payload){
        $url = self::URLCITASSERVICE . "/api/citas/cancel_all_sessions_cita";
    
        try {
            $response = Http::post($url,$payload);
            if ($response->successful()) {

                return $this->responseManager->success(null);
            }
            \Log::error("Error haciendo la peticion a la api ".$response->status());

            return $this->responseManager->serverError(
                "Error en la respuesta del servidor:" . $response->status()
            );
    
        } catch (\Exception  $e) { 
            \Log::error("Error enviando mensaje interactivo: " . $e->getMessage()); 
            return $this->responseManager->serverError(
                "Error al hacer la solicitud: " . $e->getMessage()
            );
        }
    }
}

