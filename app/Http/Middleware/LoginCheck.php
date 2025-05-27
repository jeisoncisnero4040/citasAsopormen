<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class LoginCheck
{
    public function handle(Request $request, Closure $next): Response
    {    
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json(['status' => 200], 200);
        }

        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided',
                'status' => 401,
            ], 401);
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            JWTAuth::setToken($token)->parseToken()->authenticate();
            $response = $next($request);
            $response->headers->set('Authorization', 'Bearer ' . $token);
            return $response;

        } catch (TokenExpiredException $e) {
            try {
                $newToken = JWTAuth::setToken($token)->refresh();
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer ' . $newToken);
                return $response;

            } catch (JWTException $e) {
                return response()->json([
                    'message' => 'Failed to refresh token',
                    'status' => 401,
                    'error' => $e->getMessage(),
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Invalid token',
                'status' => 401,
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
