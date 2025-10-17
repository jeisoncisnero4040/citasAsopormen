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
    public function saveAudit(string $audit):array{
        $response=$this->auditRepository->saveAudit(audit:$audit,modulo:'clinico');
        return $this->responseManager->created($response);
    }

    public function saveAuditPrintEvos(array $request, string $ip): array
    {
        $ids = isset($request['ids']) && is_array($request['ids'])
            ? implode(', ', $request['ids'])
            : '';

        $template = AuditTemplates::renderPrintEvoAudit(
            nombre: $request['profesional'] ?? '',
            ids: $ids,
            client: $request['client'] ?? '',
            from:$request['from']??'',
            to:$request['to']??'',
            ip: $ip
        );

        return $this->saveAudit(audit: $template);
    }
}