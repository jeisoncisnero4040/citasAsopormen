<?php

namespace App\Services;

use App\Dtos\GetExrenalProcedureDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\ExternalProcedurePort;
use App\Strategies\GetExternalProcedureStrategy;
use App\Strategies\DirectExternalProcedureStrategy;
use App\Strategies\ProcedureByEpsStrategy;

final class ExternalProceduresService{
    private ExternalProcedurePort $repository;
    public function __construct(ExternalProcedurePort $repository)
    {
        $this->repository = $repository;
    }
    public function getProcedures(GetExrenalProcedureDto $dto): array
    {
        if (!$dto->isValid()) {
            throw new BadRequestException('Entrada inválida: epsCode, covenantCode y tarife son requeridos.', 400);
        }
        $strategy = new GetExternalProcedureStrategy([
            new DirectExternalProcedureStrategy($this->repository),
            new ProcedureByEpsStrategy($this->repository)
        ]);
        $procedures = $strategy->resolve($dto)->execute($dto);
        return collect($procedures)
            ->map(fn($item) => $item->toArray())
            ->toArray();

    }

}