<?php
namespace App\Services;

use App\Interfaces\ReasonsPqrRepositoryInterface ;
use App\Services\BaseService;
use App\Utils\ResponseManager;
use App\Mappers\ReasonsPqrMapper;

class ReasonsPqrService extends BaseService{
    protected ResponseManager $responseManager;
    private ReasonsPqrRepositoryInterface $reasonsPqrRepository;
    private string $tag;
    public function __construct(ResponseManager $responseManager,ReasonsPqrRepositoryInterface  $reasonsPqrRepository) {
        $this->responseManager=$responseManager;
        $this->reasonsPqrRepository=$reasonsPqrRepository;
        $this->tag='Razon(es) de pqrs(s)';
    }

    public function getAllReasonsPqrs(){
        $reasons=$this->reasonsPqrRepository->getAllReasons();
        self::sourceDidFound($reasons,$this->tag);
        return $this->responseManager->success($reasons);

    }
}