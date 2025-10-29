<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\UtilitiesInterface;
use App\Utils\ResponseManager;

class UtilitiesService {
    private UtilitiesInterface $utilitiesRepository;
    private ResponseManager $responseManager;

    public function __construct(UtilitiesInterface $utilitiesRepository,ResponseManager $responseManager)
    {
        $this->utilitiesRepository=$utilitiesRepository;
        $this->responseManager=$responseManager;
    }

    public function getEvoUtilities(): array
    {
        $utils = $this->utilitiesRepository->getEvoUtilities();
        return $this->responseManager->success(
            collect($utils)
                ->map(fn($util) => $util->toArray())
                ->toArray()
        );
    }

    public function searchDx(array $requests){
        $param=$requests['dx'];
        if(!trim($param)){
            throw new BadRequestException("El text de diagnostico debe ser un texto valido",400);
        }
        $dxs=$this->utilitiesRepository->searchDx(strtoupper($param));
        return $this->responseManager->success(
            collect($dxs)->map(fn($dx)=>$dx->toArray())->toArray()
        );
    }
}