<?php

namespace App\Strategies\Evo\Search;

use App\Dtos\GetEvoDto;
use App\Repositories\EvoRepository;

class EvoByAuthorizationStrategy implements EvoSearchStrategy {
    public function __construct(private EvoRepository $repo, private string $method) {}

    public function search(GetEvoDto $dto): array {
        return $this->repo->{$this->method}(
            historia: $dto->getHistory(),
            autoriz: $dto->getAutoriz()
        );
    }
}
