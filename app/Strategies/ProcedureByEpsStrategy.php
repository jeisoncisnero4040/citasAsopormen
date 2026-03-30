<?php

namespace App\Strategies;
use App\Dtos\GetExrenalProcedureDto;
use App\Interfaces\ExternalProcedurePort;
use App\Interfaces\ExternalProcedureExtrategy;


class ProcedureByEpsStrategy implements ExternalProcedureExtrategy
{
    private ExternalProcedurePort $externalProcedurePort;
    public function __construct(ExternalProcedurePort $externalProcedurePort)
    {
        $this->externalProcedurePort = $externalProcedurePort;
    }

    public function support(GetExrenalProcedureDto $dto): bool
    {
        
        return $dto->getEpsCode() !== null && $dto->getCovenantCode() !== null;
    }
    /**
     * @return array<ExternalProcedure>
     */
    public function execute(GetExrenalProcedureDto $dto): array
    {
        return $this->externalProcedurePort->getProceduresByEps($dto->getEpsCode(),$dto->getCovenantCode());
    }
}