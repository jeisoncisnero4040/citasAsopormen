<?php
namespace App\Utils;
use Vinkla\Hashids\Facades\Hashids;


class UrlEncoder{

    public static function getHashIdAttribute($id)
    {
        $idEncoded=Hashids::encode($id);
        $baseUrl=env("URL_WEB_CLINICO_ASOPORMEN",'https://asopormen.co/helpdesk/pqrs/responder');
        return "{$baseUrl}/{$idEncoded}";
    }
    public static function getIdFromHasing($hashing){
        return Hashids::decode($hashing);
    }
}