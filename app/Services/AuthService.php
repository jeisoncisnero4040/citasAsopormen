<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;
use App\Models\ClientModel;
use App\Requests\AuthRequest;
Use App\Models\User;
use App\Repositories\RolesAndPermissionsRepository;
use App\utils\JwtGenerator;
use App\utils\ResponseManager;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService{


    private $userService;
    private JwtService $jwt;
    private $responseManager;
    private RolesAndPermissionsRepository $rolesAndPermissions;
    public function __construct(User $userService, ResponseManager $responseManager, JwtService $jwt,RolesAndPermissionsRepository $rolesAndPermissions){
        $this->userService=$userService;
        $this->responseManager=$responseManager;
        $this->jwt=$jwt;
        $this->rolesAndPermissions=$rolesAndPermissions;
    }
    public function login($request){
        AuthRequest::loginRequestValidate( $request);
        $user = $this->userService::where('cedula', $request['cedula'])
        ->select('cedula','usuario','password','estado','permisomc','rol_id','ID AS id' )
        ->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            throw new BadRequestException("Credenciales incorrectas", 400);
        }

        if ($user->estado=='INACTIVO'){
            throw new BadRequestException( "El usuario no se encuentra activo",400);
        }
        if ($user->permisomc !='1' ){
            throw new BadRequestException( "El usuario no tiene permisos para esta acción",400);
        }
        $permissions= $this->rolesAndPermissions->getPermissionsByRole((int) $user->rol_id);
        $user->permission = collect($permissions)->pluck('nombre')->toArray();
        unset($user->password);
        $token = JWTAuth::fromUser($user);
        $response=['message'=>'succes',
                    'status'=>200,
                    'access_token'=>$token,
                    'data'=>$user
                ];
                    
        return $response;
    }
    public function loginClient($request){
        AuthRequest::loginRequestValidate( $request);
        $clients=$this->sendQueryToGetClientWithPassword($request);
        $client=$this->takeFirstClient($clients);

        if (!$client || !Hash::check($request['password'], $client->password)) {
            throw new BadRequestException("Credenciales incorrectas", 400);
        }

        $token =$this->generateToken($client);

         
        $response = [
            'message' => 'success',
            'status' => 200,
            'access_token' => $token,
            'data' => $client
        ];
    
        return $response;
    }



    public function validateTokenRequest($token){
        $this->jwt->validateToken($token);
    }
    public function logout ($request)  {
 
            $token = $request->header('Authorization');
            if (!$token) {
                return $this->responseManager->success('logout exitoso',200);
            }
            $token = str_replace('Bearer ', '', $token);
            try {
                $this->jwt->invalidateToken($token);
                return $this->responseManager->success('logout exitoso');
            } 
            catch (\Exception $e) {
                throw new ServerErrorException($e->getMessage(),500);
            }
        
    }

    public function refresh($token){ 
        return  $this->jwt->refreshToken($token);
    }
    public function me($token) {
        try {
            return $this->jwt->getUserByToken($token);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    private function sendQueryToGetClientWithPassword(array $request){
        $clientCedula=$request['cedula'];
        try{
            $client=DB::select("
                SELECT TOP 1
                LTRIM(RTRIM(cli.nombre)) as nombre,
                LTRIM(RTRIM(cli.codigo)) AS codigo,
                cli2.user_password_mc AS password,
                1 AS rol
                from cliente cli 
                INNER JOIN cliente2 cli2 ON cli.codigo=cli2.codigo
                WHERE cli.nit_cli= ?

            ",[$clientCedula]);
            return $client;
        }catch(\Exception $e){
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    private function generateToken($payload){
        $JwtGenerator=new JwtGenerator($payload);
        return $JwtGenerator->jwt();
    }
    private function takeFirstClient(array $clientList)
    {
        if (empty($clientList)) {
            throw new NotFoundException(
                "No se ha encontrado un usuario con la cédula proporcionada. 
                Por favor, inténtalo de nuevo con un número de identificación válido.",
                404
            );
        }
    
        return $clientList[0];
    }
    
}