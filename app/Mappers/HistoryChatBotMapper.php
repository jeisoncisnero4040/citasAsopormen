<?php
namespace App\Mappers;

use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Collection;

class HistoryChatBotMapper {
    public static function map(array $unmMappedHistory) {
 
        $historyCollection = collect($unmMappedHistory);

 
        $historyCollection = $historyCollection->map(function($message,$index) {
            $message['id']=$index+1;
            $message["accion"] = ($message["from"] == "whatsapp:+573151938239") ? "recibido" : "enviado";
            unset($message['to']);
            unset($message['from']);
            unset($message['sid']);

 
            if (isset($message['body'])) {
                $message['mensaje'] = $message['body'];
                unset($message['body']);
            }
            if (isset($message['date_sent'])) {
                $message['fecha'] = $message['date_sent'];
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