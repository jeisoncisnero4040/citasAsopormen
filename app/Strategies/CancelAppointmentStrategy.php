<?php
namespace App\Strategies;

use App\Contracts\ActionHandlerStrategy;
use App\Domain\IaResponse;
use App\Domain\Chat;
use App\Enums\ChatStatusEnum;
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
use App\Domain\Appoinment;

class CancelAppointmentStrategy implements ActionHandlerStrategy, chatActivable, ChatStatusUpdatable, Resumable
{
    private FakeAppointmentsService $appointmentsService;
    private QueueService $queueService;
    private IaService $iaService;
    private ChatsService $chatsService;
    private array $errors = [];
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
        return $iaResponse->hasAction() && $iaResponse->action() === ChatStatusEnum::CANCEL_APPOINTMENT->value;
    }

    public function handle(IaResponse $iaResponse, Chat $chat): void
    {   
        $appoinmentToCancel = $this->resolveAppoimentToCancel($iaResponse);
        if (!$appoinmentToCancel) {
            error_log("No se pudo resolver la cita a cancelar. Errores: " . implode(", ", $this->errors));
            $chat->setErrors($this->errors);
            $this->generateResume($chat);
            $this->chatsService->save($chat);
            $this->active($chat);
            return;
        }
        logger()->info("Cita a cancelar resuelta", ['appointment' => $appoinmentToCancel->toArray()]);
        $response=$this->appointmentsService->cancelAppointment($appoinmentToCancel);
        if(!$response->success()){
            $this->errors[] = "Error al cancelar la cita: " . $response->message();
        }
        if($response->success()){
            $chat->setMetadata([
                ...$chat->getMetadata(),
                'canceled_appointments' => $appoinmentToCancel->toArray()
            ]);
            $this->setStatus($chat, ChatStatusEnum::COMPLETED);
        }
        $chat->setErrors($this->errors);
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
        logger()->info("Respuesta resumen IA", $response->toArray());
        $chat->setResume($response->getData()['resume'] ?? null);
        $chat->setMessages([]);//fallback limpieza de mensajes para no hacer tan extenso el contexto, ya que el resumen debe contener la información relevante de la conversación.
        


        return $chat;
    }
    private function resolveAppoimentToCancel(IaResponse $iaResponse): ?Appoinment
    {
        $data = $iaResponse->getData();
        logger()->info("Resolviendo cita a cancelar con data IA", $data);
        $appoinments = $data['citas'] ?? [];
        logger()->info("Citas extraídas del payload", ['appoinments' => $appoinments]);
        $reason = $data['reason'] ?? null;
        if (empty($appoinments)) {
            $this->errors[] = "No se proporcionaron citas para cancelar.";
            return null;
        }
        if (!$reason) {
            $this->errors[] = "No se proporcionó un motivo de cancelación.";
            return null;
        }
        $appoinmentInfo = $appoinments[0] ?? null;
        logger()->info("Citas en el contexto del chat", ['appoinmentInfo' => $appoinmentInfo]);
        $appointment = Appoinment::fromArray($appoinmentInfo);
        $appointment->setRazonCancelacion($reason);
        
        return $appointment;
    }
}