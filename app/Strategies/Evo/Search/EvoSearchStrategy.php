<?php

namespace App\Strategies\Evo\Search;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;

interface EvoSearchStrategy {
    public function search(GetEvoDto $dto,ProcediproModel $procedipro): array;
}
