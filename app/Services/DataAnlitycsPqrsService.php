<?php
namespace App\Services;

use App\Services\BaseService;
use App\Interfaces\AnalitycsRepositoryInterface;
use App\Mappers\PqrsMapper;
use App\Requests\PqrValidator;
use App\Utils\ResponseManager;

class DataAnlitycsPqrsService extends BaseService
{
    public ResponseManager $responseManager;
    private AnalitycsRepositoryInterface $dataAnalitycsRepository;

    public function __construct(ResponseManager $responseManager, AnalitycsRepositoryInterface $dataAnalitycsRepository)
    {
        $this->responseManager = $responseManager;
        $this->dataAnalitycsRepository = $dataAnalitycsRepository;
    }
    public function getDataAnalisysPqrs($request){
        PqrValidator::validateDataToGetDataPqrs(data:$request);
        $mappedDaterange=PqrsMapper::mapDateRange(data:$request);
        $data=$this->dataAnalitycsRepository->pqrsHistory(from:$mappedDaterange['from'],to:$mappedDaterange['to']);
        return $this->responseManager->success($data);
        
    }
}
