<?php

namespace App\Validators;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

class CitaValidator{
        public static function ValidateCitaAndNumberClient($request){
        $validator = Validator::make($request,[
            'telephone_number' => 'required|regex:/^3\d{9}$/',
            'date'=>'required|date_format:Y-m-d H:i',
            'session_ids'=>'required|string'
            
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(),400);
        }
    }
}