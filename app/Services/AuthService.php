<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;
use App\Models\ClientModel;
use App\Requests\AuthRequest;
Use App\Models\User;
use App\utils\ResponseManager;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthService{


    private $userService;
    private $clientModel;
    private $responseManager;
    public function __construct(User $userService, ResponseManager $responseManager, ClientModel $clientModel){
        $this->userService=$userService;
        $this->responseManager=$responseManager;
        $this->clientModel=$clientModel;
    }
    public function login($request){
        AuthRequest::loginRequestValidate( $request);

        $user = $this->userService::where('cedula', $request['cedula'])
                    ->select('cedula','usuario','password','estado','permisomc')
                    ->first();
        if (!$user || !Hash::check($request['password'], $user->password)) {
            throw new BadRequestException("Credenciales incorrectas", 400);
        }

        if ($user->estado=='INACTIVO'){
            throw new BadRequestException( "El usuario no se encuentra activo",400);
        }
        if ($user->permisomc=='0'){
            throw new BadRequestException( "El usuario no tiene permisos para esta acción",400);
        }



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
        $client=$this->clientModel::select('nombre','codigo','password')
                            ->where('nit_cli',$request['cedula'])
                            ->first();

        if (!$client || !Hash::check($request['password'], $client->password)) {
            throw new BadRequestException("Credenciales incorrectas", 400);
        }
        $token = JWTAuth::fromUser($client);

         
        $response = [
            'message' => 'success',
            'status' => 200,
            'access_token' => $token,
            'data' => $client
        ];
    
        return $response;
    }

    public function logout ($request)  {
 
            $token = $request->header('Authorization');
        
            if (!$token) {
                return $this->responseManager->success('logout exitoso',200);
            }
        
             
            $token = str_replace('Bearer ', '', $token);
            try {
                JWTAuth::setToken($token)->invalidate();
                return $this->responseManager->success('logout exitoso');
            } 
            catch (\Exception $e) {
                throw new ServerErrorException($e->getMessage(),500);
            }
        
    }

    public function refresh($request){
        $token = $request->header('Authorization');
    
         
        if (!$token) {
            throw new UnAuthorizateException('token not found',401);
        }
        $token = str_replace('Bearer ', '', $token);     
        try {
            $newToken = JWTAuth::setToken($token)->refresh  ();
            return $this->responseManager->success($newToken);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(),500);
        }
    }
    public function me($request) {
        $token = $request->header('Authorization');
    
         
        if (!$token) {
            throw new UnAuthorizateException('token not found',401);
        }
        $token = str_replace('Bearer ', '', $token);     
        try {
            list($header, $payload, $signature) = explode('.', $token);

 
            $decodedPayload = json_decode(base64_decode($payload), true);
            return $this->responseManager->success($decodedPayload['sub']);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }

    }
}