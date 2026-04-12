<?php

namespace App\Kafka\Strategies;

use App\Kafka\Contracts\EventHandlerStrategy;
use App\Services\AuditService;

class AuditEventHandler implements EventHandlerStrategy
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function supports(string $event): bool
    {
        return $event === 'AUDIT_EVENT';
    }

    public function handle(array $payload): void
    {
        
        $data = $payload['data'] ?? [];
        $this->auditService->saveAudit(
            audit: $data['audit'] ?? 'unknown',
            cedula: $data['cedula'] ?? 'unknown',
            modulo: $data['modulo'] ?? 'pruebas'
        );
        
    }
}