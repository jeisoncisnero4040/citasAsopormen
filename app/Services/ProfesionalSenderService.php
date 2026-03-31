<?php

namespace App\Services;
use App\Interfaces\ProfesionalSenderPort;
use App\Dtos\GetProfesionalSerderDto;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\BadRequestException;


class ProfesionalSenderService{
    private ProfesionalSenderPort $repository;
    public function __construct(ProfesionalSenderPort $repository)
    {
        $this->repository = $repository;
    }
    public function getProfesionalSenders(GetProfesionalSerderDto $dto): array
    {
        if($dto->getCode() === null && $dto->getName() === null && $dto->getCoincidenceParam() === null) {
            throw new BadRequestException('Entrada inválida: No se han encontrado parámetros de busqueda  válidos.', 400);
        }
        if($dto->getCoincidenceParam() !== null && $dto->isParamACode()) {
            $dto->setCode($dto->getCoincidenceParam());
        }
        if($dto->getCoincidenceParam() !== null && $dto->isParamAName()) {
            $dto->setName($dto->getCoincidenceParam());
        }
        $senders = $this->repository->getProfesionalSenders($dto);
        if (empty($senders)) {
            throw new NotFoundException('No se encontraron profesionales remitentes para los criterios proporcionados.', 404);
        }
        return collect($senders)
            ->map(fn($item) => $item->toArray())
            ->toArray();
    }
}