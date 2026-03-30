<?php


namespace App\Interfaces;

use App\Dtos\GetExrenalProcedureDto;

interface ExternalProcedureExtrategy{
    public function support(GetExrenalProcedureDto $dto): bool;
    public function execute(GetExrenalProcedureDto $dto): array;
}