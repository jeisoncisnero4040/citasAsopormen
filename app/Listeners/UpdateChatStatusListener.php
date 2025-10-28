<?php
namespace App\Listeners;

use App\Events\MessageSentEvent;
use App\Services\ChatsService;
use App\Services\WhatsappService;

class UpdateChatStatusListener
{
    private ChatsService $chatsService;
    private WhatsappService $whatsappService;

    public function __construct(ChatsService $chatsService, WhatsappService $whatsappService)
    {
        $this->chatsService = $chatsService;
        $this->whatsappService=$whatsappService;
    }

    public function handle(MessageSentEvent $event)
    {
        $this->whatsappService->sendMessage($event->message,"whatsapp:+57$event->numCel");
        $this->chatsService->updateChatStatus($event->numCel, $event->newStatus);
    }
}

