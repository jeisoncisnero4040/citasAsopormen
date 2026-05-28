<?php

namespace App\Interfaces;

use App\Commands\CapacitacionMediaCommand;
use App\Dtos\GetCapacitacionMediaDto;
use App\Models\CapacitacionMediaViewModel;

interface CapacitacionMediaPort
{
    public function create(CapacitacionMediaCommand $item): int;
    /**
     * @return CapacitacionMediaViewModel[]
     */
    public function get(GetCapacitacionMediaDto $dto): array;
}