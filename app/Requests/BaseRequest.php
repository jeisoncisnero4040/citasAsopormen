<?php

namespace App\Requests;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

class BaseRequest
{
    public static function validateRequest(array $request, array $rules, array $errors, array $keysAllowed): void
    {
        self::validateKeysRequest($request, $keysAllowed);
        self::validateRules($request, $rules, $errors);
    }

    protected static function validateKeysRequest(array $request, array $keysAllowed): void
    {
        $keysRequest = array_keys($request);
        $invalidKeys = array_diff($keysRequest, $keysAllowed);

        if (!empty($invalidKeys)) {
            $keysNotAllowedInString = implode(', ', $invalidKeys);
            throw new BadRequestException("Los campos {$keysNotAllowedInString} no son permitidos", 400);
        }
    }

    private static function validateRules(array $request, array $rules, array $errors): void
    {
        $validator = Validator::make($request, $rules, $errors);
        self::checkValidator($validator);
    }

    private static function checkValidator($validator): void
    {
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->first(), 400);
        }
    }
}
