<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {   
   
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
        } catch (JWTException $e) {
             
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
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
            }
 
            return response()->json([
                'message' => 'Invalid token',
                'status' => 401,
                'error' => $e->getMessage(),
            ], 401);
        }
        
  
        return $next($request);
    }
}
