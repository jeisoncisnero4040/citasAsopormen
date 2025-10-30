<?php

namespace App\Strategies\Evo;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;
use App\Strategies\EvoSearchStrategyFactory;

class PsicoEvoStrategy implements EvoStrategy {
    public function __construct(
        private EvoSearchStrategyFactory $factory,
    ) {}

    public function getEvolutions(GetEvoDto $dto,ProcediproModel $procedipro): array {
        $search = $this->factory->make($dto,
            byDateMethod: 'getPsicoEvoInRangeTime', 
            byAuthMethod: 'getPsicoEvoByAuthorization'
        );

        return $search->search($dto,$procedipro);
    }
}