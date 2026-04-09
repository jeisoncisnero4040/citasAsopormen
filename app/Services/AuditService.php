<?php

namespace App\Services;

use App\Constants\AuditTemplates;
use App\Interfaces\AuditInterface;
use App\utils\ResponseManager;

class AuditService {
    private AuditInterface $auditRepository;
    private ResponseManager $responseManager;

    public function __construct(AuditInterface $auditRepository, ResponseManager $responseManager)
    {
        $this->auditRepository=$auditRepository;
        $this->responseManager=$responseManager;
    }
    public function saveAudit(string $audit,string $cedula,$modulo='pruebas'):array{
        $auditLower=strtolower($audit);
        $this->auditRepository->saveAudit(audit:$auditLower,modulo:$modulo,cedula:$cedula);
        return $this->responseManager->created([]);
    }

}