<?php

namespace App\Services;
use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Http;
use Exception;

class DriveService {


    public function uploadDriveService(array $payload)
    {
        try {
            $url = $this->buildRequestUrl('/upload-list-citas/pdf');
            
    
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload); 
    
            if ($response->failed()) {
                throw new ServerErrorException($response->body(), $response->status());
            }
    
            return $response->json();
        } catch (Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    
    

    private function buildRequestUrl($endpoint)
    {   
        $baseUrl = "http://192.168.19.152:8082/";

        if (!$baseUrl) {
            throw new ServerErrorException("La URL base del servicio de WhatsApp no está configurada.", 500);
        } 

        return rtrim($baseUrl, '/') . $endpoint;
    }
}