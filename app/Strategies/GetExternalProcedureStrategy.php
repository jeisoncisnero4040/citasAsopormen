<?php

namespace App\Strategies;
use App\Dtos\GetExrenalProcedureDto;
use App\Interfaces\ExternalProcedureExtrategy;

class GetExternalProcedureStrategy{
    /**
     * @var ExternalProcedureExtrategy[]
     */
    private array $strategies = [];

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function resolve(GetExrenalProcedureDto $dto): ExternalProcedureExtrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($dto)) {
                return $strategy;
            }
        }

        throw new \InvalidArgumentException(
            'No se encontró estrategia válida para los parámetros enviados.'
        );
    }
}