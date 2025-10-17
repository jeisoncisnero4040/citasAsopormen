<?php

namespace App\Strategies\Evo;

use App\Dtos\GetEvoDto;

interface EvoStrategy {
    public function getEvolutions(GetEvoDto $dto): array;
}
