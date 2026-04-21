<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function __construct(){
        
    }
    public function increment(string $key, int $ttl): int
    {
        try {
            $value = Redis::incr($key);
            if ($value === 1) {Redis::expire($key, $ttl);}
            return $value;
        } catch (\Exception $e) {
            throw new ServerErrorException(
                "Error en rate limit: " . $e->getMessage(),
                500
            );
        }
    }
}
