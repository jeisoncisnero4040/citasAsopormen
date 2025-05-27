<?php
namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtService
{
    /**
     * Genera un token para un usuario dado.
     */
    public function generateToken($user): string
    {
        return JWTAuth::fromUser($user);
    }

    /**
     * Retorna el usuario autenticado desde el token actual.
     */
    public function getAuthUser()
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return null;
        }
    }


    /**
     * Invalida el token actual.
     */
    public function invalidateToken(): bool
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }

    /**
     * Refresca un token.
     */
    public function refreshToken(): ?string
    {
        try {
            return JWTAuth::parseToken()->refresh();
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Retorna el token actual si está presente.
     */
    public function getToken(): ?string
    {
        return JWTAuth::getToken();
    }
}
