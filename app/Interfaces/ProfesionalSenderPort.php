<?php
namespace App\Interfaces;

use App\Dtos\GetProfesionalSerderDto;

interface ProfesionalSenderPort
{
    /**
     * @return array<ProfesionalSender>
     */
    public function getProfesionalSenders(GetProfesionalSerderDto $dto): array;
}   