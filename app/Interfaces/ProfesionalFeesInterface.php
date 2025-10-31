<?php

namespace App\Interfaces;

use App\Models\FeeModel;

interface ProfesionalFeesInterface{
    public function getFeesProfesional(string $cedula,string $procedipro,string $entidad):FeeModel;
}