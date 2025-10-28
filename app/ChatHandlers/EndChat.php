<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\WhatsappService;
use App\Events\MessageSentEvent;
use App\Events\MessageInterativeEvent;
use App\Utils\WhatsappTemplates;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Jobs\MakeAndSendPdfCitas;

class EndChat{
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
    public function endChatDriver()
    {
        switch ($this->status) {
            case 9.0:
                $this->getDriveChat($this->buttonPayload);
                return;
            default:
                event(new MessageInterativeEvent($this->numCel, $this->text, 4.0, 0));
                return;
            
        }
    }
    private function getDriveChat($text)
    {
        switch (strtolower($text)) {
            case "menu":
                $chat=$this->chatsService->getChatByNumCel($this->numCel);
                $client=$chat['data']['data_user']['nombre'];
                event(new MessageInterativeEvent($client,$this->status, 1.0,$this->numCel));
                return;
            case "cerrar":
                $msm = "un gusto haberte atendido, hasta la próxima";
                event(new MessageSentEvent($this->numCel, $msm,10.0));
                $this->chatsService->deleteChatByCelNumber($this->numCel);
                return;
            default:
                event(new MessageInterativeEvent($this->numCel, $this->text, 4.0, 0));
                return;
            
        }
    }
    
}