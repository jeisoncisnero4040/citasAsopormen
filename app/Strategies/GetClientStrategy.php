<?php

namespace App\Strategies;

use App\Contracts\ActionHandlerStrategy;
use App\Domain\IaResponse;
use App\Domain\Chat;
use App\Ports\ClientsPort;
use App\Services\QueueService;
use App\Contracts\chatActivable;
use App\Services\ChatsService;
use App\Domain\ChatKafkaMessage;
use App\Contracts\ChatStatusUpdatable;
use App\Enums\ChatStatusEnum;
use App\Contracts\Resumable;
use App\Services\IaService;
use App\Domain\PromptBuilder;
use App\Config\Prompt as PromptConfig;



class GetClientStrategy implements ActionHandlerStrategy, 
                                    chatActivable, ChatStatusUpdatable, Resumable
{
    private ClientsPort $clientsPort;
    private QueueService $queueService; 
    private ChatsService $chatsService;
    private IaService $iaService;   
    public function __construct(ClientsPort $clientsPort, 
                                QueueService $queueService, 
                                ChatsService $chatsService,
                                IaService $iaService)
    {
        $this->clientsPort = $clientsPort;
        $this->queueService = $queueService;
        $this->chatsService = $chatsService;
        $this->iaService = $iaService;

    }
    public function supports(IaResponse $iaResponse): bool
    {
        return $iaResponse->hasAction() && $iaResponse->action() === ChatStatusEnum::VERIFY_IDENTITY->value;
    }

    public function handle(IaResponse $iaResponse, Chat $chat): void
    {   

        $this->chatsService->save($chat);
        $clientData = $this->clientsPort->getData($iaResponse->getData()['document'] ?? null);
        $client=$clientData->data();
        $chat->setClientName($client['name']);
        $this->setStatus($chat, ChatStatusEnum::IDENTIFIED);
        $this->generateResume($chat);
        $chat->setMetadata(['client_data' => $client]);
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
        $chat->setMessages([]);
        return $chat;
    }
}