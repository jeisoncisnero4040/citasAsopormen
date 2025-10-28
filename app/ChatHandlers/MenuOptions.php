<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\WhatsappService;
use App\Events\MessageSentEvent;
use App\Utils\WhatsappTemplates;
use App\Events\MessageInterativeEvent;

class MenuOptions{
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

    public function menuDriver(){
        $text=strtolower($this->text);
        switch ($text){
            case 'programar una orden':
                event(new MessageInterativeEvent(null,1.1,2.0,$this->numCel));
                return;
            case  "consultar rrdenes":
                event(new MessageInterativeEvent($text,1.2,3.0,$this->numCel));
                return;

            case "consultar citas":
                event(new MessageInterativeEvent($text,1.3,4.0,$this->numCel));
                return;
            case "cancelar cita":
                event(new MessageInterativeEvent($text,1.4,5.0,$this->numCel));
                return;
            case "pqr":
                $msm="funcion no implementada aún";
                event(new MessageSentEvent($this->numCel, $msm, 6.0));
                return;
            case "salir" :
                $msm = "un gusto haberte atendido, hasta la próxima";
                event(new MessageSentEvent($this->numCel, $msm,10.0));
                $this->chatsService->deleteChatByCelNumber($this->numCel);
                return;
            default:
                return;

        }
    }
}