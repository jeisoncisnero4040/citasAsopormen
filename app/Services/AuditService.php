<?php

namespace App\Services;

use App\Constants\AuditTemplates;
use App\Dtos\GetAuditDto;
use App\Exceptions\CustomExceptions\NotFoundException;
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
    public function saveAudit(string $audit,string $cedula,$modulo='citas'):array{
        $auditLower=strtolower($audit);
        $this->auditRepository->saveAudit(audit:$auditLower,modulo:$modulo,cedula:$cedula);
        return $this->responseManager->created([]);
    }
    public function getAudits(GetAuditDto $dto):array{
        if(!$dto->hasIdAppoinment() && !$dto->hasAuthCode() && !$dto->hasRangeTime()){
            throw new NotFoundException(message:"Un rango de fechas o un criterio de búsqueda específico (ID de cita o código de autorización) es requerido para obtener auditorías.",status:400);
        }
        $audits=$this->auditRepository->getAudits($dto);

        if(empty($audits)){
            throw new NotFoundException(message:"No se encontraron auditorías con los criterios proporcionados.",status:404);
        }
        return $audits;
    }

}