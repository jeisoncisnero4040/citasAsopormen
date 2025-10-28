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
use App\Constants\StatusChatConstans;

class ConsultCitas{
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private CitasService $citasService;
    private ChatsService $chatsService; 

    private const TEXTCITAS='continuar';
    private const TEXTHISTORY='historial';

    private const RETURNTOMENU='atras';

    public function __construct(float $status, string $numCel, ?string $text, ?string $buttonPayload,?string $urlMedia, CitasService $citasService, ChatsService $chatsService) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia=$urlMedia;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;


    }
    public function consultCitasDriver()
    {
        switch ($this->status) {
            case StatusChatConstans::INDEXCONSULTCITASPROCESS:
                $this->makeInformeCitas($this->buttonPayload);
                return;
            default:
                event(new MessageSentEvent($this->numCel, $this->text, 4.0, 0));
                return;
            
        }
    }
    private function makeInformeCitas($text)
    {
        switch (strtolower($text)) {
            case self::TEXTCITAS:
                $chat=$this->chatsService->getChatByNumCel($this->numCel);
                $client=$chat['data']['data_user'];
                dispatch(new MakeAndSendPdfCitas($client, $this->numCel,StatusChatConstans::INDEXCONSULTCITASPROCESS,$history=false))->onQueue('imagenes');
        
                $msm = "Se esta procesando su solicitud, por favor espere en linea";
                event(new MessageSentEvent($this->numCel, $msm,StatusChatConstans::ESPECIALSTATUSONWHATING));
                return;
            case self::TEXTHISTORY:
                $chat=$this->chatsService->getChatByNumCel($this->numCel);
                $client=$chat['data']['data_user'];
                dispatch(new MakeAndSendPdfCitas($client, $this->numCel,StatusChatConstans::INDEXCONSULTCITASPROCESS,$history=true))->onQueue('imagenes');
        
                $msm = "Se esta procesando su solicitud, por favor espere en linea";
                event(new MessageSentEvent($this->numCel, $msm,StatusChatConstans::ESPECIALSTATUSONWHATING));
                return;
            case self::RETURNTOMENU:
                $chat=$this->chatsService->getChatByNumCel($this->numCel);
                $client=$chat['data']['data_user']['nombre']??'Usuario';
                event(new MessageInterativeEvent($client, 
                    StatusChatConstans::INDEXCLOSEDCHAT,
                    StatusChatConstans::MENUSTATUS,
                    $this->numCel));

            default:
                return ;
        }
    }
}