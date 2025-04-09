<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Mappers\AuditMappers;
use App\Models\AuditModel;
use App\Utils\ResponseManager;
use App\Requests\AuditRequest;

class AuditService
{
    protected AuditModel $auditModel;
    protected ResponseManager $responseManager;

    public function __construct(AuditModel $auditModel, ResponseManager $responseManager)
    {
        $this->auditModel = $auditModel;
        $this->responseManager = $responseManager;
    }
    public function createAudit($audit)
    {
        AuditRequest::newAuditValidate($audit);
        $auditCreated = $this->auditModel->create($audit);
        return $this->responseManager->created($auditCreated);
    }
    public function getAudits($modulo, $data)
    {
        AuditRequest::validateDataToGetAudits($data);
        $datesMapped = AuditMappers::mapDatesToGetAudit($data);

        $audits = $this->auditModel->getRegisterByModule(
            $modulo,
            $datesMapped['from'] ?? null,
            $datesMapped['to'] ?? null
        );
        $this->sourceDidFound($audits);
        return $this->responseManager->success($audits);
    }
    public function searchAudit($searchParam)
    {
        $audits = $this->auditModel->searchRegister($searchParam);
        $this->sourceDidFound($audits);
        return $this->responseManager->success($audits);
    }
    private function sourceDidFound($response)
    {
        if (empty($response)) {
            throw new NotFoundException("Registro de auditoría no encontrado", 404);
        }
    }
    public function  createUnMappedAudit($data){
        $mappedData=AuditMappers::mapRegisterFromModules(($data));
        return $this->createAudit($mappedData);
    }
}
