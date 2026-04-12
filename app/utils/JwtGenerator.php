<?php

namespace App\utils;

use Carbon\Carbon;
use \Firebase\JWT\JWT;

class JwtGenerator{
    
    const SECRET_KEY = 'JWT_SECRET';  
    const TTL = 'JWT_TTL'; 

    private $id;
    private $now;
    private $expiration;

    public function __construct($id)
    {
        $this->id = $id;
        $this->now = Carbon::now()->timestamp;
        $this->expiration = Carbon::now()->addMinutes((int)env(self::TTL))->timestamp; 
    }

    public function jwt()
    {
        
        $payload = [
            "sub" => $this->id,  
            "iat" => $this->now , 
            "exp" => $this->expiration 
        ];

        
        return JWT::encode($payload, env(self::SECRET_KEY), 'HS256');
    }
}
