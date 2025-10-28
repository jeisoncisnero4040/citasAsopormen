<?php
namespace App\Services;
use App\Utils\ResponseManager;
use Carbon\Carbon;
use App\Services\ChatsService;
use App\Driver\ChatDriver;

 

class WhatsappService   {
    private $responseManager;
    private $twilioService;
    private $chatsService;


    public function __construct(ResponseManager $responseManager, TwilioService $twilioService,ChatsService $chatsService)
    {
        $this->responseManager = $responseManager;
        $this->twilioService = $twilioService;
        $this->chatsService = $chatsService;

    }


    public function handleClientMessage($request) {
        $numberClient = $request['From'];
        $text = $request['Body'] ?? null;   
        $mediaUrl = $request['MediaUrl0'] ?? null;   
        $buttonPayload = $request['ButtonPayload'] ?? null;  
        $messageType=$request['MessageType']??null;
    
        $numberClientCleaned = $this->cleanTelephoneNumber($numberClient);
        $status = $this->getStatusChat($numberClientCleaned);
    
        if ($status == 0) {
            $this->addChat($numberClientCleaned);
        }
        ChatDriver::handleChat($status, $numberClientCleaned, $text, $mediaUrl, $buttonPayload,$messageType);
    }
    private function cleanTelephoneNumber($telephone)
    {
         
        if (strpos($telephone, 'whatsapp:+57') === 0) {
            return str_replace('whatsapp:+57', '', $telephone);
        }
        return $telephone;
    }
    public function sendMessage($message, $number,$type='onlyText')
    {   
        return $this->twilioService->sendWhatsAppMessage($number, $message,$type);  
    }
    private function getstatusChat(string $numberCel){
        $response=$this->chatsService->getChatByNumCel($numberCel);
        if($response['status']==404){
            return 0;
        }
        return $response['data']['status'];
    }
    private function addChat($numberCel){
        return $this->chatsService->addChat($numberCel);
    }
    private function getUrlsMediaFromMessage($request){
        
    }
}