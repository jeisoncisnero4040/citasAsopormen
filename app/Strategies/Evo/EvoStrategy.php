<?php

namespace App\Strategies\Evo;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;

interface EvoStrategy {
    public function getEvolutions(GetEvoDto $dto,ProcediproModel $procedipro): array;
}
