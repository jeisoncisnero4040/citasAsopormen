<?php

namespace App\Strategies;

use App\Dtos\GetEvoDto;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Repositories\EvoRepository;
use App\Strategies\Evo\Search\EvoByAuthorizationStrategy;
use App\Strategies\Evo\Search\EvoByDateRangeStrategy;
use App\Strategies\Evo\Search\EvoSearchStrategy;

class EvoSearchStrategyFactory {
    public function __construct(private EvoRepository $repo) {}

    public function make(GetEvoDto $dto,string $byDateMethod, string $byAuthMethod): EvoSearchStrategy {
        if ($dto->getFrom() && $dto->getTo()) {
            return new EvoByDateRangeStrategy($this->repo, $byDateMethod);
        }
        if ($dto->getAutoriz()) {
            return new EvoByAuthorizationStrategy($this->repo, $byAuthMethod);
        }

        throw new ServerErrorException("Debe indicar rango de fechas o autorización", 500);
    }
}