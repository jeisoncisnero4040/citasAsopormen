<?php

namespace App\Kafka\Strategies;

use App\Domain\Chat;
use App\Kafka\Contracts\EventHandlerStrategy;
use App\Services\ChatsService;

class ChatUpdatedHandler implements EventHandlerStrategy

{
    private ChatsService $chatsService; 
    public function __construct(ChatsService $chatsService)
    {
        $this->chatsService = $chatsService;
    }
    public function supports(string $event): bool
    {
        return $event === 'chat.recieved' || $event === 'chat.updated';
    }

    public function handle(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $this->chatsService->reactToMessage(id: $data['id'] ?? null);
    }
}