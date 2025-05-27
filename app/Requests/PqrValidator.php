<?php

namespace App\Requests;

use App\Requests\BaseRequest;
use App\Constants\PqrRequestsConstants;

class PqrValidator extends BaseRequest
{
    public static function validateNewPqrRequest(array $newPqrData): void
    {
        $keysAllowed = PqrRequestsConstants::KEYS_ALLOWED_TO_ADD_PQR;
        $rules = PqrRequestsConstants::RULES_TO_ADD_PQR;
        $errors = PqrRequestsConstants::MESSAGES_ERROR_ADD_PQR;

        self::validateRequest(
            request: $newPqrData,
            rules: $rules,
            errors: $errors,
            keysAllowed: $keysAllowed
        );
    }
    public static function validateDataToSaveAnswersPqrsFromArea(array $data){
        $keysAllowed = PqrRequestsConstants::REQUEST_KEYS_TO_ANSWER_PQRS_FROM_AREA;
        $rules = PqrRequestsConstants::RULES_TO_ANSWER_PQRS_FROM_AREA;
        $errors = PqrRequestsConstants::MESSAGES_ERROR_ANSWER_PQRS_FROM_AREA;

        self::validateRequest(
            request: $data,
            rules: $rules,
            errors: $errors,
            keysAllowed: $keysAllowed
        );
    }
    public static function ValidateDataToChangeAreaPqrs(array $data){
        $keysAllowed = PqrRequestsConstants::KEYS_ALLOWED_TO_CHANGE_AREA;
        $rules = PqrRequestsConstants::RULES_TO_CHANGE_AREA;
        $errors = PqrRequestsConstants::MESSAGES_ERROR_CHANGE_AREA;

        self::validateRequest(
            request: $data,
            rules: $rules,
            errors: $errors,
            keysAllowed: $keysAllowed
        );
    }
    public static function validateDataToGetDataPqrs(array $data){
        $keysAllowed = PqrRequestsConstants::KEYS_ALLOWED_DATES_RANGE;
        $rules = PqrRequestsConstants::RULES_DATES_RANGE;
        $errors = PqrRequestsConstants::MESSAGES_ERROR_DATES_RANGE;

        self::validateRequest(
            request: $data,
            rules: $rules,
            errors: $errors,
            keysAllowed: $keysAllowed
        );
    }
}
