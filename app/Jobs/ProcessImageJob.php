<?php

namespace App\Jobs;

use App\Services\ApiFilesService;
use App\Services\ChatsService;
use App\Events\MessageSentEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\MessageInterativeEvent;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $urlMedia;
    protected string $numCel;
    protected float $currentStatus;
    protected float $nextStatus;
    protected ?string $nextTemplate;
    protected string $keyToUpdate;


    /**
     * Create a new job instance.
     */
    public function __construct(string $urlMedia,string $numCel, float $currentStatus, float $nextStatus,?string $nextTemplate, string $keyToUpdate)
    {
        $this->urlMedia = $urlMedia;
        $this->numCel = $numCel;
        $this->currentStatus = $currentStatus;
        $this->nextStatus = $nextStatus;
        $this->nextTemplate = $nextTemplate;
        $this->keyToUpdate = $keyToUpdate;
    }

    /**
     * Execute the job.
     */
    public function handle(ApiFilesService $apiFileService, ChatsService $chatsService)
    {
        try {
            $topicList = [
                2.1 => 'front_cc',
                2.2 => 'back_cc'
            ];
            
            $topic = $topicList[$this->currentStatus] ?? 'document';

            // Enviar imagen a la API de archivos
            $response = $apiFileService->processAndUploadImage([
                'url' => $this->urlMedia,
                'topic' => $topic
            ]);

            // Verificar si la respuesta es exitosa
            if (!empty($response['status']) && $response['status'] == 200) {
                $url = $response['data']['url'] ?? null;

                if ($url) {
                    $responseChat = $chatsService->getChatByNumCel($this->numCel);
                    $chat = $responseChat['data'] ?? [];

                    // Actualizar la orden con la URL de la imagen
                    $imagesOrderUrls = $chat['order_data'] ?? [];
                    $imagesOrderUrls[$this->keyToUpdate] = $imagesOrderUrls[$this->keyToUpdate] ?? [];
                    $imagesOrderUrls[$this->keyToUpdate][] = $url;

                    $chatsService->updateKeyInChat($this->numCel, 'order_data', $imagesOrderUrls);

                    if (!$this->nextTemplate){
                        event(new MessageInterativeEvent(null,$this->currentStatus,$this->currentStatus,$this->numCel)); 
                        return ;
                    }
                    event(new MessageSentEvent($this->numCel, $this->nextTemplate, $this->nextStatus));
                    return;
                }
            }

            // Manejo de error si la imagen no se pudo cargar
            $errorMessage = $response['error'] ?? "Error desconocido";
            Log::error("❌ Error en ProcessImageJob: " . $errorMessage);

            $msm = match ($response['status'] ?? null) {
                400 => "⚠️ *Error al cargar la imagen* ⚠️\n\n📌 *Detalle:* $errorMessage\n\nPor favor intenta de nuevo.",
                default => "⚠️ *Error inesperado* ⚠️\n\nNo pudimos cargar la imagen en este momento. Por favor intenta más tarde."
            };

            event(new MessageSentEvent($this->numCel, $msm, $this->currentStatus));
        } catch (\Exception $e) {
            Log::error("❌ Excepción en ProcessImageJob: " . $e->getMessage());
            $msm = "⚠️ *Error crítico* ⚠️\n\nHa ocurrido un problema inesperado. Por favor intenta más tarde.";
            event(new MessageSentEvent($this->numCel, $msm, $this->currentStatus));
        }
    }
}
