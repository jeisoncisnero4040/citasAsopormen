<?php

namespace App\Strategies\Evo;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;
use App\Strategies\EvoSearchStrategyFactory;

class RehabilitationEvoStrategy implements EvoStrategy{
    public function __construct(
        private EvoSearchStrategyFactory $factory,
    ) {}

    public function getEvolutions(GetEvoDto $dto,ProcediproModel $procedipro): array {
        $search = $this->factory->make($dto,
            byDateMethod: 'getEvoIntegralReabilitationByRangeDate', 
            byAuthMethod: 'getAbaEvoByAuthorization'
        );

        return $search->search($dto,$procedipro);
    }
}