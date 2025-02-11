<?php

namespace App\Validators;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

class ChatValidator {

    public static function startChatValidateRequest($request){
        $validator = Validator::make($request,[
            'client'=>'required|string',
            'profesional'=>'required|string',
            'observations'=>'required|string',
            'telephone_number' => 'required|regex:/^3\d{9}$/',
            'date'=>'required|date_format:Y-m-d H:i',
            'procedim'=>'required|string',
            'direction'=>'required|string',
            'session_ids'=>'required|string'
        ]);

        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400) ;
        }
    }
    public static function failedMsmValidate($request){
        $validator = Validator::make($request,[

            'telephone_number' => 'required|regex:/^3\d{9}$/',
            'date'=>'required',
            'origin'=>'required|string'
        ]);

        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400) ;
        }
    }
    public static function RetrievePasswordValidate($request){
        $validator = Validator::make($request,[

            'telephoneNumber' => 'required|regex:/^3\d{9}$/',
            'password'=>'required',
            'clientName'=>'required|string'
        ]);

        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400) ;
        }
    }
    public static function validateDataToConfirmProgramationOrden($request){
        $validator = Validator::make($request,[
            'client'=>'required|string',
            'week_days'=>'required',
            'first_day'=>'required|string',
            'authorization'=>'required',
            'laterDay'=>'required|string',
            'telephone_number'=>'required|regex:/^3\d{9}$/'
        ]);

        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400) ;
        }

    }

}