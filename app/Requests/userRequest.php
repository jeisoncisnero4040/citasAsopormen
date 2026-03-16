<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;

 

class UserRequest{
    public static function  validateEmailAndCedula($request){
        $validator=Validator::make($request,[
            'cedula' => 'required|regex:/^\d+$/',
            'email'=>'required|email'
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400);

        }

    }
    public static function validatePasswordAndCedula($request){
        $validator=Validator::make($request,[
            'cedula' => 'required|regex:/^\d+$/',
            'oldPassword'=>'required',
            'newPassword'=>'required|min:8'
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400);

        }
    }
}