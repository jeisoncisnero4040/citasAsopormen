<?php

namespace App\Services;

use App\Constants\AuditTemplates;
use App\Interfaces\AuditInterface;
use App\Utils\ResponseManager;

class AuditService {
    private AuditInterface $auditRepository;
    private ResponseManager $responseManager;

    public function __construct(AuditInterface $auditRepository, ResponseManager $responseManager)
    {
        $this->auditRepository=$auditRepository;
        $this->responseManager=$responseManager;
    }
    public function saveAudit(string $audit,string $cedula):array{
        $auditLower=strtolower($audit);
        $response=$this->auditRepository->saveAudit(audit:$auditLower,modulo:'pruebas',cedula:$cedula);
        return $this->responseManager->created($response);
    }

}