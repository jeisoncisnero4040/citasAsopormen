<?php

namespace App\Services;
use App\Interfaces\ProfesionalSenderPort;
use App\Dtos\GetProfesionalSerderDto;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Dtos\CreateProfesionalSender;
use App\Commands\ProfesionalSenderCommand;
use App\Models\UserRequesting;
use App\Services\QueueService;

class ProfesionalSenderService extends BaseService{
    private ProfesionalSenderPort $repository;
    protected QueueService $queueService;
    public function __construct(ProfesionalSenderPort $repository, QueueService $queueService)
    {
        parent::__construct($queueService);
        $this->repository = $repository;
        $this->queueService = $queueService;
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
    public function create(CreateProfesionalSender $dto,UserRequesting $user): int
    {
        $command = ProfesionalSenderCommand::fromDto($dto);
        $command ->setUserRequesting($user);
        $idNew = $this->repository->create($command);
        $msmAudit=$command->logCreate();
        $this->dispatchToQueue($msmAudit,$user);
        return $idNew;
    }
    public function utility(): array
    {
        return $this->repository->utility();
    }
}