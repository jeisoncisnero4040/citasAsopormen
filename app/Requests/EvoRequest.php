<?php
namespace App\Requests;

use App\Constants\RequestEvoConst;
use App\Exceptions\CustomExceptions\BadRequestException;

class EvoRequest extends BaseRequest
{
    public static function validateGetEvos(array $request)
    {
        $rules    = RequestEvoConst::RULES_TO_GET_EVO;
        $messages = RequestEvoConst::MESSAGES_TO_GET_EVO;

        self::validateRequest(
            request: $request,
            rules: $rules,
            errors: $messages,
            keysAllowed: array_keys($rules)
        );

        self::validateRangeOrAuthorization(request: $request);
    }

    private static function validateRangeOrAuthorization(array $request)
    {
        if (!isset($request['from']) && !isset($request['autoriz'])) {
            throw new BadRequestException(
                'Debe seleccionar un rango de fechas completo o indicar una autorización para buscar evoluciones.',
                400
            );
        }
    }
}
