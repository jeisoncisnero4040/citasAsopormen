<?php
namespace App\Services;


use App\Interfaces\UtilsPqrRepositoryInterface;
use App\Utils\ResponseManager;
use App\Services\BaseService;


class UtilsPqrService extends  BaseService {
    protected ResponseManager $responseManager;
    private UtilsPqrRepositoryInterface $utilsRepository;
    private string $tag;

    public function __construct(ResponseManager $responseManager, UtilsPqrRepositoryInterface $utilsRepository) {
        $this->responseManager=$responseManager;
        $this->utilsRepository=$utilsRepository;
        $this->tag='utilería(S)';
    }

    public function getUtility():array{
        $utility=$this->utilsRepository->getAll();
        return $this->driveResponsePersistence($utility,$this->tag);
    }
}