<?php

namespace App\Dtos;

use InvalidArgumentException;
use App\Enums\CapacitacionMediaModulesEnum;

class GetCapacitacionMediaDto
{
    private CapacitacionMediaModulesEnum $module;
    public function __construct(
        CapacitacionMediaModulesEnum $module = CapacitacionMediaModulesEnum::CITAS
    ) {
        $this->module = $module;
    }

    public function getModule(): CapacitacionMediaModulesEnum
    {
        return $this->module;
    }
    public function setModule(CapacitacionMediaModulesEnum $module): void
    {
        $this->module = $module;
    }
}