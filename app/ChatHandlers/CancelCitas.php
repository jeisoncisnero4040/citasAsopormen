<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Events\MessageSentEvent;
use App\Events\MessageInterativeEvent;
use Carbon\Carbon;
use App\Constants\StatusChatConstans;

class CancelCitas {
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private CitasService $citasService;
    private ChatsService $chatsService;  

    private const USERISREADY = 'continuar';
    private const RETURNTEXT ='atras';
    
    public function __construct(float $status, string $numCel, ?string $text, ?string $buttonPayload, ?string $urlMedia, CitasService $citasService, ChatsService $chatsService) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia = $urlMedia;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
    }

    public function cancelCitasDriver() {
        switch ($this->status) {
            case StatusChatConstans::INDEXCANCELCITAPROCESS:
                $this->makeInformeCitas($this->buttonPayload);
                return;
            case StatusChatConstans::HANDLEIDSTOCANCEL:
                $this->handleIdsToCancel();
                break;
            case StatusChatConstans::HANDLERAZONTOCANCELCITA:
                $this->handleRazonToCancelCita();
                break;
            default:
                return;
        }
    }

    private function makeInformeCitas($text) {
        switch (strtolower($text)) {
            case self::USERISREADY:
                $citas = $this->getCitasClient();
                if (empty($citas)) {
                    $msm = "Al parecer no tienes Citas disponibles para cancelar, puedes checkar si tienes citas programadas volviendo al menú y dando la opción de consultar citas.";
                    event(new MessageInterativeEvent($msm,
                                                    StatusChatConstans::INDEXCLOSEDCHAT,
                                                    StatusChatConstans::INDEXCLOSEDCHAT,
                                                    $this->numCel));
                    return;
                }
                $citasCancelables = $this->getTop10CitasCancelables($citas);
                $this->chatsService->addKeysToChat($this->numCel, ['citas_cancelables' => $citasCancelables]);
                event(new MessageInterativeEvent($citasCancelables, 
                                                StatusChatConstans::INDEXCANCELCITAPROCESS,
                                                StatusChatConstans::HANDLEIDSTOCANCEL,
                                                $this->numCel));
                break;
            case self::RETURNTEXT:
                $chat=$this->chatsService->getChatByNumCel($this->numCel);
                $client=$chat['data']['data_user']['nombre']??'Usuario';
                event(new MessageInterativeEvent($client, 
                    StatusChatConstans::INDEXCLOSEDCHAT,
                    StatusChatConstans::MENUSTATUS,
                    $this->numCel));


            default:
                return;
        }
    }

    private function getCitasClient() {
        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $client = $chat['data']['data_user'] ?? null;
        if (!$client) {
            return [];
        }
        $response = $this->citasService->getCitasClient($client['codigo']);
        return $response['data'] ?? [];
    }

    private function getTop10CitasCancelables($citas) {
        $citasCancelables = [];
        foreach ($citas as $cita) {
            if ($cita['cancelada'] != '1' && !$cita['asistida'] && !$cita['no_asistida']) {
                $citasCancelables[] = $cita;
                if (count($citasCancelables) == 10) {
                    break;
                }
            }
        }
        return $citasCancelables;
    }

    private function handleIdsToCancel() {
        $cita = $this->getCitaWhitIds();
        if (!$cita || !$this->checkCitaIsOnTime($cita)) {
            $msm = "Uppps, al parecer la cita que deseas cancelar no está disponible para esta acción porque faltan menos de 6 horas para iniciar.";
            event(new MessageInterativeEvent($msm, StatusChatConstans::HANDLEIDSTOCANCEL,
                                            StatusChatConstans::INDEXCLOSEDCHAT,
                                            $this->numCel));
            return;
        }
        $this->chatsService->addKeysToChat($this->numCel, ['ids_to_cancel' => $this->text]);
        $this->chatsService->removeKeyToChat($this->numCel, 'citas_cancelables');
        $msm = "Por favor, ingresa una observación por qué no puedes asistir a la cita.";
        event(new MessageSentEvent($this->numCel, $msm, StatusChatConstans::HANDLERAZONTOCANCELCITA));
    }

    private function getCitaWhitIds() {
        $idsSelected = $this->text;
        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $citasCancelables = $chat['data']['citas_cancelables'] ?? [];
        $filteredCitas = array_filter($citasCancelables, fn($cita) => $cita['ids'] == $idsSelected);
        return !empty($filteredCitas) ? reset($filteredCitas) : null;
    }

    private function checkCitaIsOnTime($cita) {
        $now = Carbon::now();
        $dateCita = Carbon::parse($cita['start']);
        return $now->diffInHours($dateCita, false) >= 6 && $now->isBefore($dateCita);
    }

    private function handleRazonToCancelCita() {
        $this->sendCitaToCancel();   
    }

    private function sendCitaToCancel() {
        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $ids = $chat['data']['ids_to_cancel'] ?? null;
        if (!$ids) {
            return;
        }
        $payload = [
            'ids' => $ids,
            'meanCancel' => 'Asis virtual',
            'razon' => $this->text,
            'fecha_cita' => Carbon::now()->format('Y-m-d H:i')
        ];
        $response = $this->citasService->cancelCita($payload);
        if (!empty($response) && isset($response['status']) && $response['status'] == 200) {
            $msm = "Hecho!!! Tu cita ha sido cancelada.";
            event(new MessageInterativeEvent($msm,
                                            StatusChatConstans::HANDLERAZONTOCANCELCITA, 
                                            StatusChatConstans::INDEXCLOSEDCHAT,
                                            $this->numCel));
        } else {
            event(new MessageInterativeEvent("Error al cancelar la cita", 
                                            StatusChatConstans::ESPECIALSTATUSONERROR,
                                            StatusChatConstans::INDEXCLOSEDCHAT,
                                            $this->numCel));
        }
    }
}