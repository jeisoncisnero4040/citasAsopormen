<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use PhpParser\Node\Expr\Throw_;

class ClientRequest{
    public static function historyIdValidate($request){
        $validator = Validator::make($request, [
            'historyId' => 'required|regex:/^\d+$/',   
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }  
    }
    public static function ValidateDataToRequestPassword($request){
        $validator = Validator::make($request, [

            'clientIdentity' => 'required|regex:/^\d+$/',  
            'sendPasswordToEmail' => 'boolean',  
            'sendPasswordToMobile' => 'boolean'  
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function ValidateNewPassword($request){
        $validator=Validator::make($request,[
            'password'=>'required|min:6|string',
            'clientIdentity'=>'required|regex:/^\d+$/'
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400);
        }
    }
    public static function validateDataToUpdateClient($request)
    {
        // Validar los campos permitidos
        $validator = Validator::make($request, [
            'clientCod'=>'required|string',
            'cel' => 'string|regex:/^3\d{9}$/',
            'email' => 'email'
        ]);
    
        
        $allowedKeys = ['cel', 'email','clientCod'];
        $extraKeys = array_diff(array_keys($request), $allowedKeys);
    
        if (!empty($extraKeys)) {
            throw new BadRequestException(implode(', ', $extraKeys). " no son permitidas", 400);
        }
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    

}