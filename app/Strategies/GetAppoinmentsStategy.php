<?php

namespace App\Strategies;

use App\Contracts\ActionHandlerStrategy;
use App\Domain\IaResponse;
use App\Domain\Chat;
use App\Enums\ChatStatusEnum;
use App\Domain\Client;
use App\Services\FakeAppointmentsService;
use App\Domain\ChatKafkaMessage;
use App\Services\QueueService;
use App\Contracts\chatActivable;
use App\Contracts\ChatStatusUpdatable;
use App\Contracts\Resumable;
use App\Services\IaService;
use App\Domain\PromptBuilder;
use App\Config\Prompt as PromptConfig;
use App\Services\ChatsService;

class GetAppoinmentsStategy implements ActionHandlerStrategy, chatActivable, ChatStatusUpdatable, Resumable
{
    private FakeAppointmentsService $appointmentsService;
    private QueueService $queueService;
    private IaService $iaService;
    private ChatsService $chatsService;
    public function __construct(FakeAppointmentsService $appointmentsService,
                                QueueService $queueService, 
                                IaService $iaService,
                                ChatsService $chatsService)

    {   $this->appointmentsService = $appointmentsService;
        $this->queueService = $queueService; 
        $this->iaService = $iaService;   
        $this->chatsService = $chatsService;
    }


    public function supports(IaResponse $iaResponse): bool
    {
        return $iaResponse->hasAction() &&
          $iaResponse->action() === ChatStatusEnum::CONSULTING_APPOINTMENTS->value;
    }

    public function handle(IaResponse $iaResponse, Chat $chat): void
    {   
        $client =Client::fromArray(
            $chat->getMetadata()['client_data'] ?? []
        );
        $appointments = $this->appointmentsService->getAppointmentsForClient($client);
        $appointmentsData = $appointments->data() ?? [];
        $appointmentsArray = array_map(function($appointment) {
            return $appointment->toArray();
        }, $appointmentsData);
        $chat->setMetadata([
            ...$chat->getMetadata(),
            'appointments' => $appointmentsArray
        ]);
        logger()->info('Citas obtenidas para el cliente', [
            'client_id' => $client->getId(),
            'appointments' => $appointmentsArray
        ]); 
        $this->setStatus($chat, ChatStatusEnum::CANCELING_APPOINTMENTS);
        $this->generateResume($chat);
        $this->chatsService->save($chat);
        $this->active($chat);

    }
    public function active(Chat $chat): void
    {
        $messageEvent = ChatKafkaMessage::createFromChat($chat,'chat.updated');
        $this->queueService->publish($messageEvent);
    }
    public function setStatus(Chat $chat, ChatStatusEnum $status): Chat
    {
        $chat->setStatus($status);
        return $chat;
    }
    public function generateResume(Chat $chat): Chat
    {

        $prompt = PromptBuilder::fromChat($chat)
            ->withModel('gpt-5-nano')
            ->withRole('system')
            ->withStream(PromptConfig::RESUME)
            ->build();
        $response = $this->iaService->executePrompt($prompt);
        $chat->setResume($response->getData()['resume'] ?? null);
        $chat->setMessages([]);//fallback limpieza de mensajes para no hacer tan extenso el contexto, ya que el resumen debe contener la información relevante de la conversación.
        


        return $chat;
    }
}