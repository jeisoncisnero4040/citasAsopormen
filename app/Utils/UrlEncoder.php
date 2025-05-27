<?php
namespace App\Utils;
use Vinkla\Hashids\Facades\Hashids;


class UrlEncoder{

    public static function getHashIdAttribute($id)
    {
        $idEncoded=Hashids::encode($id);
        $baseUrl=env("URL_WEB_CLINICO_ASOPORMEN",'http://127.0.0.1:3000/clinico/pqrs/responder');
        return "{$baseUrl}/{$idEncoded}";
    }
    public static function getIdFromHasing($hashing){
        return Hashids::decode($hashing);
    }
}