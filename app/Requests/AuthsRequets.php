<?php

namespace App\Requests;

use App\Constants\ConstRequestsAuths;

class AuthsRequets extends BaseRequest{

    public static function validateDataToCloseAuth(array $data){
        $errors=ConstRequestsAuths::MESSAGES_TO_CLOSE_AUTH;
        $rules=ConstRequestsAuths::RULES_TO_CLOSE_AUTH;
        $keysAllowed=ConstRequestsAuths::FIELDS_TO_CLOSE_AUTH;
        self::validateRequest(request:$data,rules:$rules,errors:$errors,keysAllowed:$keysAllowed);
    }
}