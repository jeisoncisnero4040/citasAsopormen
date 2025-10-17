<?php

namespace App\Requests;

use App\Constants\ConstantsRequest;

class AuthRequests extends BaseRequest{

    public static function validateLoginData(array $request):void{
        $rules=ConstantsRequest::RULES_TO_LOGIN;
        $errors=ConstantsRequest::ERRORS_TO_LOGIN;
        $keys=ConstantsRequest::FIELDS_TO_LOGIN;

        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keys);
    }
    public static function validateChangePassword(array $request):void{
        $rules=ConstantsRequest::RULES_TO_CHANGE_PASSWORD;
        $errors=ConstantsRequest::ERRORS_TO_CHANGE_PASSWORD;
        $keys=ConstantsRequest::FIELDS_TO_CHANGE_PASSWORD;

        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keys);
    }

}