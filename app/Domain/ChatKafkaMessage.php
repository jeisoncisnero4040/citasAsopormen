<?php

namespace App\Domain;
use App\Kafka\Domain\MessageQueue;

class ChatKafkaMessage 
{
    public static function createFromChat(Chat $chat,string $eventType='chat.recieved'): MessageQueue
    {
        return MessageQueue::create('crm', $eventType)
            ->withData($chat->toQueue())
            ->withKey($chat->getId());

    }
}   