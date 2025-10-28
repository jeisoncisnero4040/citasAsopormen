<?php

namespace App\Listeners;

use App\Events\MessageInterativeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ChatsService;
use App\Services\TwilioService;
use App\Events\MessageSentEvent;

class MessageInterativeListener
{
    private ChatsService $chatsService;
    private TwilioService $twilioService;

    public function __construct(ChatsService $chatsService, TwilioService $twilioService)
    {
        $this->chatsService = $chatsService;
        $this->twilioService = $twilioService;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageInterativeEvent $event): void
    {   

        $status = $event->getCurrentStatus();  

        switch ($status) {
            case 0.0:
                
                $this->twilioService->sendInteractiveMessageLoginUser($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 0.1:
                $this->twilioService->sendInterativeMenuMessage($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 0.3:
                
                $this->twilioService->sendInterativeListEps($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 0.6:
                $this->twilioService->sendInterativeMenuMessage($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 1.0:
                return;
            case 1.1:
                $this->twilioService->sendInterativeMessageUploadOrder($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 1.2:
                $this->twilioService->consultOrdersInterativeMessage($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;

                
            case 1.3:
                $this->twilioService->sendInterativeMessageToCheckCitas($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 1.4:
                $this->twilioService->sendInterativeMessageCancelCita($event->getNumCel());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 2.25:
                $this->twilioService->requestParticularUserHaveHistoryClinical($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 2.5:
                $this->twilioService->sendRequestNextClinicalHistoryImageInterativeMessage($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 2.65:
                $this->twilioService->sendHandleObservationsInterativeMessage($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 2.7:
                $this->twilioService->sendHandleObservationsInterativeMessage($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 2.75:
                $this->twilioService->sendWhatsAppMessage($event->getNumCel(),$event->getData());
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 3.0:
                $this->twilioService->sendInterativeMessageWhithOrdersCase($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;
            case 4.0:
                $this->twilioService->sendInterativeMessageWithPdfCitas($event->getNumCel(),$event->getData());
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;

            case 4.1:
                $this->twilioService->sendWhatsAppMessage($event->getNumCel(),$event->getData());
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 5.0:
                $this->twilioService->sendInterativeMessageListCitasCancelables($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 5.1:
                $this->twilioService->sendWhatsAppMessage($event->getNumCel(),$event->getData());
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 5.2:
                $this->twilioService->sendWhatsAppMessage($event->getNumCel(),$event->getData());
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            case 9.0:
                $this->twilioService->sendInterativeMenuMessage($event->getNumCel(),$event->getData() );
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return;
            
            case 10.1:
                $this->twilioService->sendInterativeMessageError($event->getNumCel(),$event->getData() );
                $this->twilioService->sendEndBranchChatMessageInterative($event->getNumCel(),$event->getData());
                $this->chatsService->updateChatStatus($event->getNumCel(), $event->getNextStatus());
                return ;


            default:
                event(new MessageSentEvent($event->getNumCel(), "estado no menajedo $status", 2.0, 0));
                return;
        }
    }
}
