<?php

namespace App\Strategies\Evo\Search;

use App\Dtos\GetEvoDto;

interface EvoSearchStrategy {
    public function search(GetEvoDto $dto): array;
}
