<?php
namespace App\Mappers;
use Carbon\Carbon;

class HistoryChatBotMapper {
    
    public static function map(array $unmMappedHistory) {
 
        $historyCollection = collect($unmMappedHistory);

 
        $historyCollection = $historyCollection->map(function($message,$index) {
            $message['id']=$index+1;
            $message["sender"] = ($message["from"] == "whatsapp:+573151938239" || $message["from"] == "whatsapp:+573150788964" ) ? "bot" : "usuario";
            unset($message['sid']);
            $message['from']=str_replace('whatsapp:+57','',$message['from']);
            $message['to']=str_replace('whatsapp:+57','',$message['to']);
 
            if (isset($message['body'])) {
                $message['mensaje'] = $message['body'];
                unset($message['body']);
            }
            if (isset($message['date_sent'])) {
                $message['fecha'] = Carbon::parse($message['date_sent'])->subHours(5);
                unset($message['date_sent']);
            }
            if (isset($message['status'])) {
                $message['estado'] = $message['status'];
                unset($message['status']);
            }

            return $message;
        });

        return $historyCollection;
    }
}