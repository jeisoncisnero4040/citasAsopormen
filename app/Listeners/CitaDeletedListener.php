<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use App\Events\CitaDeletedEvent;

class CitaDeletedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected AuditService $auditService;

    /**
     * Inyectamos AuditService en el constructor
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Handle the event.
     */
    public function handle(CitaDeletedEvent $event): void
    {   
        $data=$event->getMetaData();
        $data = [
            'modulo' => 'citas',
            'id'=>$event->getId(),
            'usuario' => $data['usuario'],
            'cliente'=>$data['cliente']??null,
            'profesional' => $data['profesional']??null,  
            'action'=>'eliminar_cita'
        ];

        // Guardamos auditoría
        $this->auditService->createUnMappedAudit($data);
    }
}
