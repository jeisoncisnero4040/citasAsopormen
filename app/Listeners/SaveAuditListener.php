<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use App\Events\citaCreateEvent;

class SaveAuditListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected AuditService $auditService;

    /**
     * Inyección del servicio de auditoría.
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Manejar el evento.
     */
    public function handle(citaCreateEvent $event): void
    {   
        $schedule=$event->getSchedule();
        $dataCita=$event->getDataCita();
        $range = [$schedule[0], $schedule[count($schedule) - 1]];

        $data = [
            'action'=>'create_citas',
            'ids'=>$event->getIds(),
            'range'=>$range,
            'usuario'=>$dataCita['registro'],
            'cliente'=>$dataCita['clientName'],
            'profesional'=>$dataCita['profesional'],

        ];

        // Llamada al servicio para guardar auditoría
        $this->auditService->createUnMappedAudit($data);
    }
}
