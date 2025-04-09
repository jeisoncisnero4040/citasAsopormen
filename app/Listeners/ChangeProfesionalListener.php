<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use App\Events\ChangeProfesionalEvent;

class ChangeProfesionalListener
{
    private AuditService $auditService;
    public function __construct(AuditService $auditService)
    {
        $this->auditService=$auditService;
    }

    /**
     * Handle the event.
     */
    public function handle(ChangeProfesionalEvent $event): void
    {
        $data=$event->getData();
        $data['action']='cambiar_profesional';
        $this->auditService->createUnMappedAudit($data);
    }
}
