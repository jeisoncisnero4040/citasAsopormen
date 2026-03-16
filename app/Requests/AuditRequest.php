<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;

 

class AuditRequest{
    public static function newAuditValidate($request){
        $validator = Validator::make($request, [
            'modulo' => 'required|string',
            'descripcion' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
        
    }
    public static function validateDataToGetAudits($request){
        $validator = Validator::make($request, [
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from'
        ]);
    
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
}