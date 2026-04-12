<?php

namespace App\Mappers;
use App\Domain\Chat;
use App\Domain\Message;
use App\Domain\MessageContent;
use App\Enums\ChatStatusEnum;

class ArrayToChat
{
    public static function map(array $data): Chat
    {
        return new Chat(
            $data['id'],
            $data['telephoneNumber'],
            $data['createdAt'],
            ChatStatusEnum::from($data['status']),

            array_map(
                fn($m) => new Message(
                    $m['sender'],
                    $m['receiver'],
                    MessageContent::fromContent($m['content']),
                    $m['timestamp'],
                    $m['sid'],
                    $m['urlMedia'] ?? null
                ),
                $data['messages'] ?? []
            ),
            $data['clientName'] ?? null,
            $data['identityVerified'] ?? false,
            $data['metadata'] ?? null,
            $data['errors'] ?? null,
            $data['resume'] ?? null

        );
    }
}