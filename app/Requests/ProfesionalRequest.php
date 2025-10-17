<?php

namespace App\Requests;

use App\Constants\ConstantsRequest;

class ProfesionalRequest extends BaseRequest{
    public static function validateCedula(array $request):void{
        $rules=ConstantsRequest::RULES_TO_GET_DAILY_SCHEDULE;
        $errors=ConstantsRequest::ERRORS_RULES_TO_GET_DAILY_SCHEDULE;
        $keys=ConstantsRequest::FIELDS_TO_GET_DAILY_SCHEDULE;

        SELF::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keys);
    }
    public static function validateGetScheduleRequest(array $request){
        $rules=ConstantsRequest::RULES_TO__GET_SCHEDULE;
        $errors=ConstantsRequest::ERRORS_TO_GET_SCHEDULE;
        $keys=ConstantsRequest::FIELDS_TO_GET_SCHEDULE;

        SELF::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keys);
    }
}