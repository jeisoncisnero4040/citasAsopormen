<?php

namespace App\Services;
use App\Ports\ChatRepositoryPort;
use App\Dtos\MessageDto;
use App\Domain\Message;
use App\Domain\Chat;
use App\Domain\ChatKafkaMessage;
use App\Domain\MessageContent;
use App\Domain\Prompt;
use App\Strategies\ActionHandlerRegistry;
use App\Strategies\GetClientStrategy;
use App\Enums\ChatStatusEnum;
use App\Domain\IaResponse;
use App\Strategies\PromptFactoryRegistry;
use App\Strategies\GetAppoinmentsStategy;
use App\Strategies\CancelAppointmentStrategy;

class ChatsService
{
    private ChatRepositoryPort $chatRepository;
    private QueueService $queueService; 
    private MessagingService $messagingService; 
    private IaService $iaService;           
    private PromptFactoryRegistry $promptFactory;
    public function __construct(ChatRepositoryPort $chatRepository, 
        QueueService $queueService,
        MessagingService $messagingService ,
        IaService $iaService,
        PromptFactoryRegistry $promptFactory
        )
    {
        $this->chatRepository = $chatRepository;
        $this->queueService = $queueService;
        $this->messagingService = $messagingService;
        $this->iaService = $iaService;
        $this->promptFactory = $promptFactory;
    }

    public function handleMessage(MessageDto $message): array
    {
        $messageEntity = Message::fromDto($message);
        $chat= $this->chatRepository->findByTelephone($messageEntity->getSender());
        if (!$chat) {
            $chat = $this->createNewChat($messageEntity->getSender());
        }
        logger()->info('mensaje recibido', [
            'message' => $message->getContent(),
        ]);
        $chat->addMessage($messageEntity);
        $this->chatRepository->save($chat);
        $this->sendToQueue($chat);
        return $chat->toArray();

    }
    public function createNewChat(string $telephoneNumber): Chat
    {
        
        $chat = new Chat(
            id: uniqid(),
            telephoneNumber: $telephoneNumber,
            createdAt: date('Y-m-d H:i:s'),
            status: ChatStatusEnum::NEW
        );
        $this->chatRepository->save($chat);
        return $chat;

    }
    public function getAllChats(int $limit = 50, int $offset = 0): array
    {
        $chats = $this->chatRepository->findAll($limit, $offset);
        if (!$chats) {
            return [];
        }
        return array_map(fn($chat) => $chat->toArray(), $chats);
    }
    public function save(Chat $chat): Chat
    {
        return $this->chatRepository->save($chat);
    }

    public function reactToMessage(?string $id): void
    {
        if (!$id) {
            logger()->error('ID de chat no proporcionado para reaccionar al mensaje');
            return;
        }

        $chat = $this->chatRepository->findById($id);
        if (!$chat) {
            logger()->error('Chat no encontrado para reaccionar al mensaje', ['id' => $id]);
            return;
        }
        $prompt = $this->getPayloadToIa($chat);
        $iaResponse = $this->iaService->executePrompt($prompt);
        if ($iaResponse) {
            logger()->info('respuesta IA obtenida', [
                'response' => $iaResponse->toArray()
            ]);
            return;
        }
        logger()->info('IA response obtenida', $iaResponse->toArray());
        if ($iaResponse->hasAction()) {
            logger()->info('IA action detectada', [
                'action' => $iaResponse->action,
                'data' => $iaResponse->toArray()
            ]);

            $registry = new ActionHandlerRegistry([
                app(GetClientStrategy::class),
                app(GetAppoinmentsStategy::class),
                app(CancelAppointmentStrategy::class)
            ]);

            $handler = $registry->getHandlerForResponse($iaResponse);
            if (!$handler) {
                logger()->warning('No se encontró handler para acción IA', [
                    'action' => $iaResponse->action
                ]);
                return;
            }
            $this->sendProcessingMessage($iaResponse, $chat);
            $handler->handle($iaResponse, $chat);
            logger()->info('IA action manejada por handler', [
                'action' => $iaResponse->action,
                'handler' => get_class($handler)
            ]);
            
            return; 
        }
        $this->sendMessageToClientFromIaResponse($iaResponse, $chat);;
        $this->chatRepository->save($chat); 
    }
    private function sendToQueue(Chat $chat): void
    {

        $messageEvent = ChatKafkaMessage::createFromChat($chat);
        $this->queueService->publish($messageEvent);
    }
    private function getPayloadToIa(Chat $chat): Prompt
    {
        return $this->promptFactory->build($chat);
    }
    public function dropAllChats(): void
    {
        $this->chatRepository->dropAll();
    }
    private function sendMessageToClientFromIaResponse(IaResponse $iaResponse, Chat $chat): Chat
    {
        $content = $iaResponse->message() ?? 'No entendí tu solicitud, ¿puedes repetirla?';
        $iaMessage = Message::fromApp(
            sender: env('TWILIO_WHATSAPP_FROM', 'system'),
            receiver: $chat->getTelephoneNumber(),
            content: MessageContent::fromContent($content),
        );
        $response = $this->messagingService->sendMessage($iaMessage);
        if (!$response->success()) {
            logger()->error('Error enviando mensaje IA', $response->data());
            return $chat;
        }
        $iaMessage->setSid($response->data()['sid'] ?? null);
        // 5. Persistencia
        $chat->addMessage($iaMessage);
        return $chat;

    }
    private function sendProcessingMessage(IaResponse $iaResponse, Chat $chat): Chat
    {
        return $this->sendMessageToClientFromIaResponse($iaResponse, $chat);
    }
}