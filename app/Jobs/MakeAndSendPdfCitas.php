<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DriversChatConstants;
use App\Services\ApiFilesService;
use App\Services\CitasService;
use App\Events\MessageSentEvent;
use App\Events\MessageInterativeEvent;
use App\Constants\StatusChatConstans;

class MakeAndSendPdfCitas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataClient;
    protected $numCel;
    protected $status;
    protected $isHistory;
    /**
     * Create a new job instance.
     */
    public function __construct($dataClient, $numCel,$status,$isHistory)
    {
        $this->dataClient = $dataClient;
        $this->numCel = $numCel;
        $this->status=$status;
        $this->isHistory=$isHistory;

    }

    /**
     * Execute the job.
     */
    public function handle(ApiFilesService $apiFileService, CitasService $citasService): void
    {
        
        $responseCitas = $this->isHistory?$citasService->getHistoryClient($this->dataClient['codigo']):$citasService->getCitasClient($this->dataClient['codigo']);

        if ($responseCitas['status'] == 404) {
            $msm="Hola, por el momento no tienes citas programadas. Si necesitas agendar una, avísanos y con gusto te ayudamos.";
            event(new MessageInterativeEvent($msm,StatusChatConstans::CLIENTCITASNOTFOUND,StatusChatConstans::INDEXCLOSEDCHAT,$this->numCel));
            return;
        }

        if ($responseCitas['status'] == 200) {
            $citas = $responseCitas['data'];
        
            $citaExample = $citas[0];
            $keysUndeleteables = DriversChatConstants::KEYSTOCREATEPDFCITAS;
            $keysToDelete = array_diff(array_keys($citaExample), $keysUndeleteables);
        
            
            foreach ($citas as &$cita) {
                foreach ($keysToDelete as $key) {
                    if (isset($cita[$key])) {
                        unset($cita[$key]);
                    }
                }

            }
            unset($cita); 
        
            $responseFile = $apiFileService->makeAndUploadPdfCitas($citas,$this->dataClient['nombre'],$this->isHistory);
            \Log::error("Error en pdf: " . json_encode($responseFile));
            $url = isset($responseFile['status']) && $responseFile['status'] == 200 
                ? ($responseFile['data'] ?? null) 
                : null;
        
            if (!$url) {

                event(new MessageInterativeEvent(null,StatusChatConstans::ESPECIALSTATUSONERROR,StatusChatConstans::INDEXCLOSEDCHAT,$this->numCel));
                return;
            }

            
            $urlEnd="$url/view?usp=sharing";
            
            /*enviar mensaje con url de citas*/
            event(new MessageInterativeEvent($urlEnd,$this->status,StatusChatConstans::INDEXCLOSEDCHAT,$this->numCel));
        }else{

            $errorMessage = $response['error'] ?? "Error desconocido";
            \Log::error("❌ Error en ProcessImageJob: " . $errorMessage);

            $msm = match ($response['status'] ?? null) {
                400 => "⚠️ *Error al cargar el documento* ⚠️\n\n📌 *Detalle:* $errorMessage\n\nPor favor intenta de nuevo.",
                default => "⚠️ *Error inesperado* ⚠️\n\nNo pudimos cargar el documento en este momento. Por favor intenta más tarde."
            };

            event(new MessageSentEvent($this->numCel, $msm, $this->currentStatus));
        }
    }
}
