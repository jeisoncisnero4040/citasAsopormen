<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;

 

class AuthRequest{
    public static function loginRequestValidate($request){
        $validator = Validator::make($request, [
            'cedula' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
        
    }
}