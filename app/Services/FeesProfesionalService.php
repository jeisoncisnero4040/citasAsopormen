<?php

namespace App\Services;

use App\Interfaces\ProfesionalFeesInterface;
use App\Models\FeeModel;

class FeesProfesionalService{
    private ProfesionalFeesInterface $feesRepository;

    public function __construct(ProfesionalFeesInterface $feesRepository)
    {
        $this->feesRepository = $feesRepository;
    }

    public function getFeesProfesional(string $cedula,string $procedipro,string $entidad):FeeModel{
            return $this->feesRepository->getFeesProfesional(
                cedula:$cedula,
                entidad:$entidad,
                procedipro:$procedipro
            );
    }
}