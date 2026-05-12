<?php
namespace App\Interfaces;

use App\Dtos\GetProfesionalSerderDto;
use App\Models\ProfesionalSender;
use App\Commands\ProfesionalSenderCommand;

interface ProfesionalSenderPort
{
    /**
     * @return array<ProfesionalSender>
     */
    public function getProfesionalSenders(GetProfesionalSerderDto $dto): array;
    public function create(ProfesionalSenderCommand $command): int;
    public function utility(): array;
}   