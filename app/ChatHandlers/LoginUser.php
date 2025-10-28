<?php
namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Events\MessageSentEvent;
use App\Events\MessageInterativeEvent;
use App\Utils\WhatsappTemplates;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Constants\StatusChatConstans;

class LoginUser {
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $mediaUrl;
    private CitasService $citasService;
    private ChatsService $chatsService;  

    public function __construct(float $status, string $numCel, ?string $text, ?string $buttonPayload, ?string $mediaUrl, CitasService $citasService, ChatsService $chatsService) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
        $this->mediaUrl = $mediaUrl;
    }

    public function driveLogin() {
        try {
            switch ($this->status) {
                case StatusChatConstans::INDEXLOGIN:
                    $this->handleIndexLogin();
                    break;
                case StatusChatConstans::HANDLEUSER:
                    $this->handleUserSelection();
                    break;
                case StatusChatConstans::HANDLENAMENEWUSER:
                    $this->handleNewUserName();
                    break;
                case StatusChatConstans::HANDLENUMCEDULANEWUSER:
                    $this->handleUserCedula();
                    break;
                case StatusChatConstans::HANDLEEPSNEWUSER:
                    $this->handleUserEPS();
                    break;
                case StatusChatConstans::HANDLEDIRECTIONNEWUSER:
                    $this->handleUserDirection();
                    break;
                case StatusChatConstans::HANDLEEMAILNEWUSER:
                    $this->handleUserEmail();
                    break;
                default:
                    throw new BadRequestException("Estado no manejado", 400, $this->status, $this->numCel);
            }
        } catch (\Exception $e) {
            event(new MessageSentEvent($this->numCel, $e->getMessage(), StatusChatConstans::INDEXLOGIN));
        }
    }

    private function handleIndexLogin() {
        $clients = $this->getClientsNum();
        if (empty($clients)) {
            $this->sendMessage(WhatsappTemplates::generateLoginTemplate(), StatusChatConstans::HANDLENAMENEWUSER);
        } else {
            $this->chatsService->addKeysToChat($this->numCel, ['client_by_num_cel' => $clients]);
            event(new MessageInterativeEvent($clients, StatusChatConstans::INDEXLOGIN, StatusChatConstans::HANDLEUSER, $this->numCel));
        }
    }

    private function handleUserSelection() {
        if (strtolower($this->text)=='otro'){
            $this->sendMessage(WhatsappTemplates::generateLoginTemplate(), StatusChatConstans::HANDLENAMENEWUSER);
            return;
        }
        $users = $this->getKeyToChat('client_by_num_cel');
        $dataUser = array_filter($users, fn($user) => $user['nombre'] === $this->text);
        if (empty($dataUser)) {
            throw new BadRequestException("Usuario inválido", 400, $this->status, $this->numCel);
        }
        $this->chatsService->addKeysToChat($this->numCel, ['data_user' => array_shift($dataUser)]);
        $this->chatsService->removeKeyToChat($this->numCel, 'client_by_num_cel');
        event(new MessageInterativeEvent($this->text, StatusChatConstans::HANDLEUSER, StatusChatConstans::MENUSTATUS, $this->numCel));
    }

    private function handleNewUserName() {
        $this->validateTextOnly();
        $this->updateUserData(['nombre' => $this->text, 'codigo' => null]);
        $this->sendMessage("Ahora por favor ingresa el número de cédula", StatusChatConstans::HANDLENUMCEDULANEWUSER);
    }

    private function handleUserCedula() {
        $this->validateTextOnly();
        $this->updateUserData(['cedula' => $this->text]);
        event(new MessageInterativeEvent($this->text, StatusChatConstans::HANDLENUMCEDULANEWUSER, StatusChatConstans::HANDLEEPSNEWUSER, $this->numCel));
    }

    private function handleUserEPS() {
        $this->validateTextOnly();
        $this->updateUserData(['entidad' => $this->text]);
        $this->sendMessage("Ahora ingresa tu dirección de residencia", StatusChatConstans::HANDLEDIRECTIONNEWUSER);
    }

    private function handleUserDirection() {
        $this->validateTextOnly();
        $this->updateUserData(['direccion' => $this->text]);
        $this->sendMessage("Ahora ingresa tu correo electrónico", StatusChatConstans::HANDLEEMAILNEWUSER);
    }

    private function handleUserEmail() {
        $this->validateTextOnly();
        $this->updateUserData(['email' => $this->text]);
        $this->chatsService->removeKeyToChat($this->numCel, 'client_by_num_cel');
        event(new MessageInterativeEvent($this->getKeyToChat('data_user')['nombre'], $this->status, StatusChatConstans::MENUSTATUS, $this->numCel));
    }

    private function getClientsNum(): array {
        $response = $this->citasService->getClientsByNumberCel($this->numCel);
        return $response['status'] == 200 ? ($response['data'] ?? []) : [];
    }

    private function getKeyToChat(string $key) {
        $response = $this->chatsService->getChatByNumCel($this->numCel);
        return $response['data'][$key] ?? null;
    }

    private function updateUserData(array $newData) {
        $dataUser = $this->getKeyToChat('data_user') ?? [];
        $dataUser = array_merge($dataUser, $newData);
        $this->chatsService->updateKeyInChat($this->numCel, 'data_user', $dataUser);
    }

    private function validateTextOnly() {
        if (!$this->text || $this->mediaUrl) {
            throw new BadRequestException("Mensaje inválido", 400, $this->status, $this->numCel);
        }
    }

    private function sendMessage(string $message, float $status) {
        event(new MessageSentEvent($this->numCel, $message, $status));
    }
}
