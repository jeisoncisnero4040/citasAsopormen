<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsappService
{
    const URL_WHATSAPP_SERVICE = 'WHATSAPP_SERVICE_URL'; 
    public function sendMessageToRetrievePassword(array $payload)
    {
        try {
            $url = $this->buildRequestUrl('/whatsapp/retrieve_password'); 

            
            $client = new Client();

            
            $response = $client->post($url, [
                'json' => $payload, 
            ]);

            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            
            throw new ServerErrorException( $e->getMessage(), 500);
        }
    }
    public function sendNotificationOrdenProgramed($data){
        error_log("aqui en w1") ;
    }
    public function rememberFisrtCita($data){
        
        try {
            $url = $this->buildRequestUrl('/whatsapp/start_chat'); 

            
            $client = new Client();

            
            $response = $client->post($url, [
                'json' => $data, 
            ]);

            
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            
            throw new ServerErrorException( $e->getMessage(), 500);
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
