<?php

namespace App\Requests;

use App\Constants\ConstansRequestAppoiments;

class AppoimentsRequests extends BaseRequest{
    public static function validateCancelAppoiments(array $request){
        $errors=ConstansRequestAppoiments::ERRORS_TO_CANCEL_APPOIMENT;
        $rules=ConstansRequestAppoiments::RULES_TO_CANCEL_APPOIMENT;
        $keysAllowed=ConstansRequestAppoiments::FIELDS_TO_CANCEL_APPOIMENT;
        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keysAllowed);
    }
    public static function ValidateEvoAppoiments(array $request):void{
        $errors=ConstansRequestAppoiments::ERRORS_TO_EVO_APPOS;
        $rules=ConstansRequestAppoiments::RULES_TO_EVO_APPOS;
        $keysAllowed=ConstansRequestAppoiments::FIELDS_TO_EVO_APPOS;
        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keysAllowed);
    }
    public static function validateEvoPsicology(array $request):void{
        $errors=ConstansRequestAppoiments::ERRORS_TO_EVO_APPOS_PSICO;
        $rules=ConstansRequestAppoiments::RULES_TO_EVO_APPOS_PSICO;
        $keysAllowed=ConstansRequestAppoiments::FIELDS_TO_EVO_APPOS_PSICO;
        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keysAllowed);
    }
    public static function validateEvoABA(array $request):void{
        $errors=ConstansRequestAppoiments::ERRORS_TO_EVO_APPOS_ABA;
        $rules=ConstansRequestAppoiments::RULES_TO_EVO_APPOS_ABA;
        $keysAllowed=ConstansRequestAppoiments::FIELDS_TO_EVO_APPOS_ABA;
        self::validateRequest(request:$request,rules:$rules,errors:$errors,keysAllowed:$keysAllowed);
    }
}