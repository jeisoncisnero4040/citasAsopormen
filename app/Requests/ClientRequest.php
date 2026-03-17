<?php

namespace App\Requests;


use App\Constanst\RequestClientConstants;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;


class ClientRequest extends BaseRequest{
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
    public static function ValidateNewPassword($request)
    {
        $messages = [
            'password.required'    => 'La nueva contraseña es obligatoria.',
            'password.min'         => 'La nueva contraseña debe contener al menos 8 caracteres.',
            'password.string'      => 'La nueva contraseña debe ser una cadena de texto válida.',

            'oldPassword.required' => 'La contraseña actual es obligatoria.',
            'oldPassword.string'   => 'La contraseña actual debe ser una cadena de texto válida.',

            'codigo.required'      => 'El código del usuario es obligatorio.',
            'codigo.regex'         => 'El código debe contener únicamente números.',
        ];

        $validator = Validator::make($request, [
            'password'    => 'required|min:8|string',
            'oldPassword' => 'required|string',
            'codigo'      => 'required|regex:/^\d+$/'
        ], $messages);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->first(), 400);
        }
    }


    public static function validateDataToUpdateClient(array $data)
    {
        $rules=RequestClientConstants::RULES_UPDATE_CLIENT;
        $msms=RequestClientConstants::MESSAGES_UPDATE_CLIENT;
        $keysAllowed=RequestClientConstants::KEYS_ALLOWED_UPDATE_CLIENT;
        self::validateRequest(request:$data,rules:$rules,errors:$msms,keysAllowed:$keysAllowed);
    }
    public static function validateDataCreateClient(array $data){
        $rules=RequestClientConstants::DATA_CREATE_CLIENT;
        $msms=RequestClientConstants::ERRORES_CREATE_CLIENT;
        $keysAllowed=array_keys($rules);
        self::validateRequest(request:$data,rules:$rules,errors:$msms,keysAllowed:$keysAllowed);
    }
    public static function validateDataUpdateClient(array $data){
        $rules=RequestClientConstants::DATA_UPDATE_CLIENT;
        $msms=RequestClientConstants::ERRORES_CREATE_CLIENT;
        $keysAllowed=array_keys($rules);
        self::validateRequest(request:$data,rules:$rules,errors:$msms,keysAllowed:$keysAllowed);
    }


}