<?php


namespace App\Strategies\Evo;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;
use App\Strategies\EvoSearchStrategyFactory;

class AbaEvoStrategy implements EvoStrategy{
    public function __construct(
        private EvoSearchStrategyFactory $factory,
    ) {}

    public function getEvolutions(GetEvoDto $dto,ProcediproModel $procedipro): array {
        $search = $this->factory->make($dto,
            byDateMethod: 'getAbaEvoInRangeTime', 
            byAuthMethod: 'getAbaEvoByAuthorization'
        );

        return $search->search($dto,$procedipro);
    }
}