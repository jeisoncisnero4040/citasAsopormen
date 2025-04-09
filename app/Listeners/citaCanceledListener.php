<?php

namespace App\Listeners;

use App\Events\citaCanceledEvent;
use App\Services\AuditService;

class citaCanceledListener
{
    /**
     * Create the event listener.
     */
    protected AuditService $auditService;

    public function __construct(AuditService $auditService){
        $this->auditService=$auditService;
    }
    /**
     * Handle the event.
     */
    public function handle(citaCanceledEvent $event): void
    {
        $data=$event->getMetaData();
        if($data['meanCancel']=='mc'){
            $data['action']='cancelar_cita';
            $this->auditService->createUnMappedAudit($data);
        }
    }
}
