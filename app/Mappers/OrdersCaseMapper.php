<?php
namespace App\Mappers;

use Carbon\Carbon;

class OrdersCaseMapper{
    public static function DataFromAcceptOrderInDtoToData($dataInDto){
        $dataInDto['date']=self::getNowInSmallDatetime();
        return $dataInDto;
    }
    public static function DataFromRejectInDtoToData($dataInDto){
        return self::DataFromAcceptOrderInDtoToData($dataInDto);
    }
    public static function DataFromCloseInDtoToData($dataInDto){
        return self::DataFromAcceptOrderInDtoToData($dataInDto);
    }
    public static function getNowInSmallDatetime(){
        return Carbon::now()->format('Y-m-d H:i:s');
    }
    
}