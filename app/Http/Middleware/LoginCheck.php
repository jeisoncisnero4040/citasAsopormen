<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomExceptions\ForbidenException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;
use App\Services\AuthService;
use App\Services\RolesAndPermissionsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\utils\ResponseManager;

class LoginCheck 
{
    protected AuthService $authService;
    protected ResponseManager $responseManager;
    protected RolesAndPermissionsService $rolesAndPermissions;
    public function __construct(AuthService $authService,RolesAndPermissionsService $rolesAndPermissions)
    {
        $this->authService = $authService;
        $this->responseManager = new ResponseManager();
        $this->rolesAndPermissions=$rolesAndPermissions;
    }

    public function handle(Request $request, Closure $next, ...$permissionsName): Response
    {   
        
        if ($request->isMethod('OPTIONS')) {
            return response()->json($this->responseManager->success(null));
        }
        $token = $request->bearerToken();
        if (!$token) {
            throw new UnAuthorizateException('El token de acceso no fue proporcionado', 401);
        }

        $this->authService->validateTokenRequest($token);
        $profesional = $this->authService->me($token);
        $rol = $profesional['rol'];
        $hasPermission = $this->rolesAndPermissions->checkPermissionsRole((int) $rol, ...$permissionsName);
        if (!$hasPermission) {
            throw new ForbidenException("El usuario no tiene permisos para realizar esta acción", 403);
        }
        $newToken = $this->authService->refresh($token);
        $response = $next($request);
        if ($newToken !== $token) {
            $response->headers->set('Authorization', 'Bearer ' . $newToken);
        }

        return $response;
    }

}
