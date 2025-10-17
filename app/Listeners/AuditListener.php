<?php

namespace App\Listeners;

use App\Events\AuditEvent;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $this->auditService->saveAudit($event->getAuditMessage());
    }
}
