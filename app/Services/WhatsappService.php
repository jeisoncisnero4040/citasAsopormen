<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Http;
use Exception;

class WhatsappService
{
    const URL_WHATSAPP_SERVICE = 'WHATSAPP_SERVICE_URL'; 

    public function sendMessageToRetrievePassword(array $payload)
    {
        try {
            $url = $this->buildRequestUrl('/whatsapp/retrieve_password'); 
            $response = Http::post($url, $payload);

            if ($response->failed()) {
                throw new ServerErrorException($response->body(), $response->status());
            }

            return $response->json();
        } catch (Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    public function sendNotificationOrdenProgramed($data)
    {
        
        try {
            $url = $this->buildRequestUrl('/whatsapp/confirm_programation');
            $response = Http::post($url, $data);

            if ($response->failed()) {
                throw new ServerErrorException($response->body(), $response->status());
            }
            return $response->json();
        } catch (Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }

    }

    public function rememberFisrtCita($data)
    {
        try {
            $url = $this->buildRequestUrl('/whatsapp/start_chat');
            $response = Http::post($url, $data);

            if ($response->failed()) {
                throw new ServerErrorException($response->body(), $response->status());
            }
            return $response->json();
        } catch (Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    public function getHistoryWhatsapCel($cel)
    {
        try {
            $url = $this->buildRequestUrl("/whatsapp/history/$cel");
            $response = Http::get($url);

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
        $baseUrl = env(self::URL_WHATSAPP_SERVICE);

        if (!$baseUrl) {
            throw new ServerErrorException("La URL base del servicio de WhatsApp no está configurada.", 500);
        } 

        return rtrim($baseUrl, '/') . $endpoint;
    }
}
