<?php

namespace App\Mappers;

class MessageToTwillioMapper
{
    public static function map(array $message): array
    {
        return [
            'From' => $message['from'],
            'To' => $message['to'],
            'Body' => $message['body'],
        ];
    }
}