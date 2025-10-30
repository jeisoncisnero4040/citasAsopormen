<?php

namespace App\Strategies\Evo\Search;

use App\Dtos\GetEvoDto;
use App\Models\ProcediproModel;
use App\Repositories\EvoRepository;

class EvoByDateRangeStrategy implements EvoSearchStrategy {
    public function __construct(private EvoRepository $repo, private string $method) {}

    public function search(GetEvoDto $dto,ProcediproModel $procedipro): array {
        return $this->repo->{$this->method}(
            historia: $dto->getHistory(),
            from: $dto->getFrom(),
            to: $dto->getTo(),
            procedipro:$procedipro->getNombre()


        );
    }
}
