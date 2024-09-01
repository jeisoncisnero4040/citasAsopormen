<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;

 

class ClientRequest{
    public static function historyIdValidate($request){
        $validator = Validator::make($request, [
            'historyId' => 'required|regex:/^\d+$/',   
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }  
    }

}