<?php

namespace App\Listeners;

use App\Events\AuditEvent;
use App\Services\AuditService;


class AuditListener
{
    private AuditService $auditService;
    public function __construct(AuditService $auditService)
    {
        $this->auditService=$auditService;
    }

    /**
     * Handle the event.
     */
    public function handle(AuditEvent $event): void
    {
        $this->auditService->saveAudit(audit:$event->getAuditMessage(),cedula:$event->getCedula());
    }
}
