<?php

namespace App\Services;

use App\Services\QueueService;
use App\Dtos\CreateAgreementsDto;
use App\Dtos\GetAgreementsDto;
use App\Interfaces\AgreementsPort;
use App\Commands\AgreementsCommand;
use App\Models\UserRequesting;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\AgreementsViewModel;
class AgreementsService extends BaseService
{
    private AgreementsPort $repository;
    protected QueueService $queueService;

    public function __construct(
        AgreementsPort $repository,
        QueueService $queueService
    ) {
        parent::__construct($queueService);
        $this->repository = $repository;
        $this->queueService = $queueService;
    }



    /**
     * @return AgreementsViewModel[]
     */
    public function get(GetAgreementsDto $dto): array
    {
        // implementar lógica de consulta
        $sagreements = $this->repository->get($dto);
        if(empty($sagreements)) {
            throw new NotFoundException('No se encontraron convenios con los parametros proporcionados',400);
        }
        return $sagreements;
    }
}