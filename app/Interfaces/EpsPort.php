<?php
namespace App\Interfaces;

use App\Domain\Eps;
use App\Domain\Code;
use App\Dtos\GetEpsDto;

interface EpsPort
{
    public function get(GetEpsDto $dto): array;
}