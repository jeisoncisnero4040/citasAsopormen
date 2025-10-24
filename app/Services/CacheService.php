<?php

namespace App\Services;

use App\Dtos\EvoBufferDto;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Utils\ResponseManager;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private ResponseManager $responseManager;
    private const DEFAULT_TTL = 3600 * 24 * 7 + 7200; 
    public function __construct(ResponseManager $responseManager){
        $this->responseManager=$responseManager;
    }
    public function getEvoInBuffer(EvoBufferDto $evoInBuffer)
    {
        $key = 'evo:' . $evoInBuffer->getCedula() . ':' . $evoInBuffer->getHistoria() . ':' . $evoInBuffer->getAutoriz();
        $data = $this->get(key: $key);
        if (empty($data)) {
            throw new NotFoundException("Información de evolución en caché no encontrada", 404);}
        return $this->responseManager->success($data);
    }
    public function add(string $key, mixed $data, ?int $ttl = null): void
    {
        $this->sendQuery(
            data: $data,
            verbo: 'set',
            key: $key,
            ttl: $ttl ?? self::DEFAULT_TTL
        );
    }
    

    public function get(string $key): array {
        return $this->sendQuery(verbo: 'get', key: $key);
        
    }
    public function clear(string $key): void{
        $this->sendQuery(verbo: 'clear', key: $key);
    }
    public function getAll(): array{
        try {
            $keys = Redis::keys('*');
            $results = [];
            foreach ($keys as $key) {
                $key=str_replace("clinico_asopormen_database_","",$key);
                $data = Redis::get($key);
                $results[$key] = $this->isJson($data) ? json_decode($data, true) : $data;
            }

            return $results;
        } catch (\Exception $e) {
            throw new ServerErrorException("Error al obtener todos los registros: " . $e->getMessage(), 500);
        }
    }
    private function sendQuery(mixed $data = null, string $verbo = 'get', ?string $key = null, ?int $ttl = null): mixed{
        try {
            switch ($verbo) {
                case 'set':
                    if (is_array($data) || $data instanceof \JsonSerializable) {
                        $data = json_encode($data);
                    }
                    Redis::setex($key, $ttl ?? self::DEFAULT_TTL, $data);
                    break;

                case 'get':
                    $data = Redis::get($key);
                    if ($data === null) { 
                        return [];
                    }
                    return $this->isJson($data) ? json_decode($data, true) : $data;

                case 'clear':
                    Redis::del($key);
                    break;
            }
        } catch (\Exception $e) {
            throw new ServerErrorException("Error en buffer: " . $e->getMessage(), 500);
        }

        return null;
    }

    private function isJson(?string $string): bool
    {   
        if ($string === null) {
            return false;
        }
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
