<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\CaseOrderService;
use App\Events\MessageSentEvent;
use App\Events\MessageInterativeEvent;
use App\Constants\StatusChatConstans;
use App\Utils\WhatsappTemplates;

class ConsultOrders {
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private ?string $messageType;
    private array $chatData = [];

    private CitasService $citasService;
    private ChatsService $chatsService;
    private CaseOrderService $caseOrderService;

    public function __construct(
        float $status,
        string $numCel,
        ?string $text,
        ?string $buttonPayload,
        ?string $urlMedia,
        ?string $messageType,
        CitasService $citasService,
        ChatsService $chatsService,
        CaseOrderService $caseOrderService
    ) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia = $urlMedia;
        $this->messageType = $messageType;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
        $this->caseOrderService = $caseOrderService;

        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $this->chatData = $chat['data'] ?? [];
    }

    public function consultOrdersDriver() {
        switch ($this->status) {
            case StatusChatConstans::INDEXCONSULTORDERS:
                $this->handleOptionSelectedByUser();
                break;

            case StatusChatConstans::HANDLECASESELECTED:
                $this->handleOrderSelectedByUser();
                break;

            default:
                $this->sendMessage($this->text, StatusChatConstans::DEFAULT);
        }
    }

    private function handleOptionSelectedByUser() {
        if (!$this->buttonDidSelected()) return;

        switch ($this->buttonPayload) {
            case 'continuar':
                $response = $this->getOrdersToUserChat();
                \Log::error("Error en pdf: " . json_encode($response));


                if ($response['status'] === 404) {
                    $this->sendInterativeMessage(
                        StatusChatConstans::CASESNOTFOUND,
                        StatusChatConstans::INDEXCLOSEDCHAT,
                        "Estimado Usuario al parecer no tienes ordenes cargadas actualmente"
                    );
                    return;
                }

                if ($response['status'] === 200) {
                    $this->addKeysToChat(['orders_data' => $response['data']]);

                    $this->sendInterativeMessage(
                        StatusChatConstans::INDEXCONSULTORDERS,
                        StatusChatConstans::HANDLECASESELECTED,
                        $response['data']
                    );
                    return;
                }

                $this->sendInterativeMessage(
                    StatusChatConstans::ESPECIALSTATUSONERROR,
                    StatusChatConstans::ESPECIALSTATUSONERROR,
                    "Error al obtener las órdenes"
                );
                break;

            case 'atras':
                $clientName = $this->chatData['data_user']['nombre'] ?? 'Usuario';
                $this->sendInterativeMessage(
                    StatusChatConstans::INDEXCLOSEDCHAT,
                    StatusChatConstans::MENUSTATUS,
                    $clientName
                );
                break;

            default:
                // Opción no reconocida, puede loguearse si se desea.
                break;
        }
    }

    private function handleOrderSelectedByUser() {
        $orders = collect($this->chatData['orders_data'] ?? []);
        $selectedId = (int) $this->text;

        $order = $orders->firstWhere('id', $selectedId);

        if (!$order) {
            $this->sendMessage("No se encontró la orden seleccionada.", StatusChatConstans::HANDLECASESELECTED);
            return;
        }

        // Ejemplo: mostrar resumen de la orden
        $summary = $this->getTemplateFromOrder($order);

        $this->sendInterativeMessage(5.2,StatusChatConstans::INDEXCLOSEDCHAT,$summary);
    }

    private function getOrdersToUserChat() {
        return $this->caseOrderService->getOrdersUser($this->numCel);
    }

    private function sendMessage(string $text, float $nextStatus) {
        event(new MessageSentEvent($this->numCel, $text, $nextStatus));
    }

    private function sendInterativeMessage(float $currentStatus, float $nextStatus, mixed $data) {
        event(new MessageInterativeEvent($data, $currentStatus, $nextStatus, $this->numCel));
    }

    private function buttonDidSelected(): bool {
        return !empty($this->buttonPayload);
    }

    private function addKeysToChat(array $newData) {
        $this->chatsService->addKeysToChat($this->numCel, $newData);
    }
    private function getTemplateFromOrder(array $order) {
        if ($order['rechazada'] === '1') {
            return WhatsappTemplates::orderRejectedTemplate($order);
        }
    
        if ($order['finalizada'] === '1') {
            return WhatsappTemplates::orderClosedTemplate($order);
        }
    
        if ($order['aceptada'] === '1') {
            return WhatsappTemplates::orderAceptedTemplate($order);
        }
        return WhatsappTemplates::orderInProgres($order);
    }
    
}
