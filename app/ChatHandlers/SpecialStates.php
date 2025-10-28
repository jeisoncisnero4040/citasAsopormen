<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\WhatsappService;
use App\Events\MessageSentEvent;
use App\Utils\WhatsappTemplates;


class SpecialStates{
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private CitasService $citasService;
    private ChatsService $chatsService;  

    public function __construct(float $status, string $numCel, ?string $text, ?string $buttonPayload,?string $urlMedia, CitasService $citasService, ChatsService $chatsService) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia=$urlMedia;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
    }
    public function SpecialStatusDrive(){
        switch($this->status){
            case 10.0:
                $msm= WhatsappTemplates::requestIMageInQueue();
                event(new MessageSentEvent($this->numCel, $msm, $this->status));
                return;
        }

    }
}