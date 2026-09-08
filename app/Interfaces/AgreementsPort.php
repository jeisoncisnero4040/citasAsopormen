<?php

namespace App\Interfaces;

use App\Commands\AgreementsCommand;
use App\Dtos\GetAgreementsDto;
use App\Models\AgreementsViewModel;

interface AgreementsPort
{
    public function create(AgreementsCommand $item): int;
    /**
     * @return AgreementsViewModel[]
     */
    public function get(GetAgreementsDto $dto): array;
}