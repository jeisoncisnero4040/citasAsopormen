<?php

namespace App\Services;

use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Http;
use Exception;
use Throwable;

class ApiFilesService
{
    private $responseManager;
    const URLFILESSERVICE = 'http://file-manager';

    public function __construct(ResponseManager $responseManager)
    {
        $this->responseManager = $responseManager;
    }
    
    public function processAndUploadImage(array $payload)
    {
        try {
            $url = self::URLFILESSERVICE . '/twilio/upload-file';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(60)
            ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $this->responseManager->success($data['data'] ?? []);
            }elseif($response->status()==400){
                $data = $response->json();
                return $this->responseManager->badRequest($data['error'] ?? "error desconocido");

            } else {
                return $this->responseManager->serverError("Error en la respuesta del servidor: " . $response->body());
            }
        } catch (Throwable $e) {
            return $this->responseManager->serverError("Error al hacer la solicitud: " . $e->getMessage());
        }
    }
    public function makeAndUploadPdfCitas($citas,$client,$history)
    {   
        $payload=['cliente'=>$client,
                    'citas'=>$citas,
                    'history'=>$history];
        
        try {
            $url = self::URLFILESSERVICE . '/pdf/upload-citas-whith-status';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $this->responseManager->success($data['data'] ?? []);
            } else {
                return $this->responseManager->serverError("Error en la respuesta del servidor: " . $response->body());
            }
        } catch (Throwable $e) {
            return $this->responseManager->serverError("Error al hacer la solicitud: " . $e->getMessage());
        }
    }
    public function processAndUploadPdf(array $payload)
    {
        try {
            $url = self::URLFILESSERVICE . '/twilio/upload-file/pdf';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout(60)
            ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $this->responseManager->success($data['data'] ?? []);
            }elseif($response->status()==400){
                $data = $response->json();
                return $this->responseManager->badRequest($data['error'] ?? "error desconocido");

            } else {
                return $this->responseManager->serverError("Error en la respuesta del servidor: " . $response->body());
            }
        } catch (Throwable $e) {
            return $this->responseManager->serverError("Error al hacer la solicitud: " . $e->getMessage());
        }
    }
}
