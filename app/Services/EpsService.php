<?php
namespace App\Services;

use App\Dtos\GetEpsDto;
use App\Interfaces\EpsPort;
use App\Domain\Eps;

use App\Exceptions\CustomExceptions\NotFoundException;

class EpsService extends BaseService
{
    protected QueueService $queueService;
    private  EpsPort $repo;
    
    public function __construct(QueueService $queueService,EpsPort $repo)
    {

        parent::__construct($queueService);
        $this->repo = $repo;
    }
    
    public function getEps(GetEpsDto $dto): array
    {
        return $this->repo->get($dto);
    }
    public function find(GetEpsDto $dto): Eps
    {

        $epss = $this->repo->get($dto);
        if (empty($epss)) {
            throw new NotFoundException('NO encontramos una eps con los criterios proporcionados');
        }
        return $epss[0];
    }
}