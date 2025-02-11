<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Http;
use Exception;

class ApiCitasAsopormenService
{
    const URL_CITAS_ASOPORMEN = 'API_CITAS_ASOPORMEN';
    private $maxRetries;
    private $a;

    public function __construct($maxRetries = 4, $a = 0.3)
    {
        $this->maxRetries = $maxRetries;
        $this->a = $a;
    }

    public function sendRequestToConfirmCita($request, $cont = 0)
    {
        try {
            $url = env(self::URL_CITAS_ASOPORMEN) . '/api/citas/confirm_all_sessions_cita';
            $response = $this->attemptRequest($request, $url, $cont, 'sendRequestToConfirmCita');
            

            return $response['status'] === 200;
        } catch (Exception $e) {
            \Log::error('Error confirming cita: ' . $e->getMessage());
            return false;
        }
    }

    public function sendRequestToCancelCita($request, $cont = 0)
    {
        try {
            $url = env(self::URL_CITAS_ASOPORMEN) . '/api/citas/cancel_all_sessions_cita';
            $response = $this->attemptRequest($request, $url, $cont, 'sendRequestToCancelCita');
            return $response['status'] === 200;
        } catch (Exception $e) {
            \Log::error('Error canceling cita: ' . $e->getMessage());
            return false;
        }
    }

    private function attemptRequest($request, $url, $cont, $retryMethod)
    {
        $payload = $this->buildRequest($request);

        try {
            return $this->sendRequest($payload, $url);
        } catch (Exception $e) {
            if ($cont < $this->maxRetries) {
                sleep(pow($this->a, $cont + 1));
                return $this->$retryMethod($request, ++$cont);
            }
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    private function sendRequest($payload, $url)
    {
        $response = Http::withHeaders($payload['headers'])->post($url, $payload['json']);

        if ($response->status() !== 200) {
            $error = $response->json('data.error.error') ?? 'Unknown error';
            throw new Exception($error);
        }

        return [
            'status' => $response->status(),
            'data' => $response->json()
        ];
    }

    private function buildRequest($request)
    {
        return [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $request,
        ];
    }
}

