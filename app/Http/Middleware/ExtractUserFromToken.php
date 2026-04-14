<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuthService;

class ExtractUserFromToken
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next): Response
    {

        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200);
        }
        $token = $request->bearerToken();
        if (empty(str_replace(['null', 'undefined'], '', $token))) {
            $request->attributes->set('userPayload', [
                'userRequestCedula' => null,
                'usernameRequest'   => null
            ]);
            return $next($request);
        }

        //$this->authService->validateTokenRequest($token);
        $user = $this->authService->me($token);
        $request->attributes->set('userPayload', [
            'userRequestCedula' => $user['cedula'] ?? null,
            'usernameRequest'   => $user['user'] ?? null,
        ]);

        return $next($request);
    }
}