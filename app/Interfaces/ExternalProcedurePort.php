<?php

namespace App\Interfaces;

use App\Dtos\GetExrenalProcedureDto;
use App\Models\ExternProcedure;
interface ExternalProcedurePort
{
    /**
     * @return array<ExternProcedure>
     */
    public function getProceduresByEps(string $epsCode,string $covenantCode): array;
    public function getProcedures(GetExrenalProcedureDto $dto): array;
}