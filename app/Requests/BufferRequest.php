<?php
namespace App\Requests;

use App\Constants\BufferRequestCons;

class BufferRequest extends BaseRequest{
    public static function validateGetBufferData(array $request):void{
        
        $rules=BufferRequestCons::RULES_TO_READ_EVO;
        $msms=BufferRequestCons::MESSAGES_TO_READ_EVO;
        $keys=BufferRequestCons::KEYS_TO_READ_EVO;

        self::validateRequest(request:$request,
                            rules:$rules,
                            errors:$msms,
                            keysAllowed:$keys);

    }
}