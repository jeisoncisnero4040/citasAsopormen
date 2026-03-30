<?php

namespace App\Strategies;
use App\Interfaces\ExternalProcedureExtrategy;
use App\Interfaces\ExternalProcedurePort;
use App\Dtos\GetExrenalProcedureDto;

class DirectExternalProcedureStrategy implements ExternalProcedureExtrategy
{
    private ExternalProcedurePort $externalProcedurePort;
    public function __construct(ExternalProcedurePort $externalProcedurePort)
    {
        $this->externalProcedurePort = $externalProcedurePort;
    }
    public function support(GetExrenalProcedureDto $dto): bool
    {
        return $dto->getEpsCode() === null || $dto->getCovenantCode() === null;
    }
    public function execute(GetExrenalProcedureDto $dto): array
    {
        return $this->externalProcedurePort->getProcedures(dto:$dto);
    }
}