<?php

namespace App\Services;

use App\Services\QueueService;
use App\Dtos\CreateCapacitacionMediaDto;
use App\Dtos\GetCapacitacionMediaDto;
use App\Interfaces\CapacitacionMediaPort;
use App\Commands\CapacitacionMediaCommand;
use App\Models\UserRequesting;
use App\Services\StorageService;

class CapacitacionMediaService extends BaseService
{
    private CapacitacionMediaPort $repository;
    protected QueueService $queueService;
    private StorageService $storageService;

    public function __construct(
        CapacitacionMediaPort $repository,
        QueueService $queueService
    ) {
        parent::__construct($queueService);
        $this->repository = $repository;
        $this->queueService = $queueService;
        $this->storageService = new StorageService();
    }

    public function create(CreateCapacitacionMediaDto $dto,UserRequesting $user ): array
    {

        return [];
    }

    public function get(GetCapacitacionMediaDto $dto): array
    {
        $results = $this->repository->get($dto);
        foreach ($results as &$result) {
            $signedUrl = $this->storageService->signedUrl($result->getKey());
            $result->setUrlSigned($signedUrl);
        }
        return $results;
    }
}