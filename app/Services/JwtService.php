<?php

namespace App\Services;

use App\Interfaces\JwtInterface;
use App\Models\ProfesionalModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;

class JwtService implements JwtInterface
{
    public function generateToken(ProfesionalModel $profesional): string{
        return JWTAuth::fromUser($profesional);

    }
    public function invalidateToken(string $token): void
    {
        JWTAuth::setToken($token)->invalidate();
    }

    public function refreshToken(string $token): string

    {   
        [$header, $payload, $signature] = explode('.', $token);
        $payloadDecoded = json_decode(base64_decode($payload), true);
        $expTimestamp = $payloadDecoded['exp'] ?? null;
        if (empty($payloadDecoded['exp'])) {
            throw new UnAuthorizateException('Token malformado o sin expiración', 401);
        }
        $exp = Carbon::createFromTimestamp($expTimestamp);
        $now = Carbon::now();
        if ($now->diffInSeconds($exp, false) < 600) {
            return JWTAuth::setToken($token)->refresh();
        }
        return $token;
    }

    public function getUserByToken(string $token): mixed
    {
        try {
            list($header, $payload, $signature) = explode('.', $token);
            $decodedPayload = json_decode(base64_decode($payload), true);
            $cedula=$decodedPayload['sub'];
            $rol=$decodedPayload['rol'];
            return ["cedula"=>$cedula,"rol"=>$rol];
        } catch (JWTException $e) {
            return null;
        }
    }
    public function validateToken(string $token): void
    {
        try {
            JWTAuth::setToken($token)->checkOrFail();
        } catch (TokenExpiredException $e) {
            throw new UnAuthorizateException('El token ha expirado', 401);
        } catch (TokenInvalidException $e) {
            throw new UnAuthorizateException('El token no es válido', 401);
        } catch (JWTException $e) {
            throw new UnAuthorizateException('No se pudo validar el token', 401);
        }
    }
}
