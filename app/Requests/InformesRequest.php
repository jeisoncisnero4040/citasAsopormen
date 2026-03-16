<?php

namespace App\Requests;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Support\Facades\Validator;

class InformesRequest{
    public static function validateDateRange($request){
        $validator=Validator::make($request,[
            "to"=>"required|date",
            "from"=>"required|date"
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors()->first(),400);
        }
    }
    public static function validateUserNewsData($request){
        $validator=Validator::make($request,[
            "to"=>"required|date",
            "from"=>"required|date",
            "procedure"=>"required|string"
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors()->first(),400);
        }
    }
}