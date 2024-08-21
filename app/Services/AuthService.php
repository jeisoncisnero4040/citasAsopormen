<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Exceptions\CustomExceptions\UnAuthorizateException;
use App\Requests\AuthRequest;
Use App\Models\User;
use App\utils\ResponseManager;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService{


    private $userService;
    private $responseManager;
    public function __construct(User $userService, ResponseManager $responseManager){
        $this->userService=$userService;
        $this->responseManager=$responseManager;
    }
    public function login($request){
        AuthRequest::loginRequestValidate( $request);

        $user = $this->userService::where('cedula', $request['cedula'])
                    ->select('cedula','usuario','password','estado')
                    ->first();

        if (!$user ||   $user->password !=$request['password']){
            
            throw new BadRequestException("credenciales incorrectas",400);
        }
 

        $token = JWTAuth::fromUser($user);
        $response=['message'=>'succes',
                    'status'=>200,
                    'access_token'=>$token,
                    'data'=>$user
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
            $newToken = JWTAuth::setToken($token)->refresh();
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
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                throw new NotFoundException('User not found', 404);
            }
    
            return $this->responseManager->success($user);
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }

    }
}