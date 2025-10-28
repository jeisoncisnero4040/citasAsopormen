<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\CaseOrderService;
use App\Events\MessageSentEvent;
use App\Utils\WhatsappTemplates;
use App\Jobs\ProcessImageJob;
use App\Jobs\ProcessPdfJob;
use App\Constants\StatusChatConstans;
use App\Events\MessageInterativeEvent;


use Illuminate\Support\Str;

class ChatHandlerBase {
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private ?string $messageType;
    private CitasService $citasService;
    private ChatsService $chatsService;
    private ?CaseOrderService $caseOrderService;  

    public function __construct(
        float $status, 
        string $numCel, 
        ?string $text, 
        ?string $buttonPayload, 
        ?string $urlMedia,
        ?string $messageType, 
        CitasService $citasService, 
        ChatsService $chatsService,
        ?CaseOrderService $caseOrderService
    ) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia = $urlMedia;
        $this->messageType = $messageType;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
        $this->caseOrderService=$caseOrderService;

        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $this->chatData=$chat['data'] ?? [];
        
    }
    protected function sendMessage(string $message, float $status) {
        event(new MessageSentEvent($this->numCel, $message, $status));
    }
    protected function getKeyToChat(string $key) {
        $response = $this->chatsService->getChatByNumCel($this->numCel);
        return $response['data'][$key] ?? null;
    }
    protected function updateUserData(array $newData) {
        $dataUser = $this->getKeyToChat('data_user') ?? [];
        $dataUser = array_merge($dataUser, $newData);
        $this->chatsService->updateKeyInChat($this->numCel, 'data_user', $dataUser);
    }

}