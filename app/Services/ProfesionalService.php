<?php
namespace App\Services;

use App\Dtos\UpdateProDto;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\ProfesionalRepositoryInterface;
use App\Requests\ProfesionalRequest;
use App\Services\AuthsService;
use App\Utils\ResponseManager;
use Illuminate\Http\UploadedFile;


class ProfesionalService{
    private AppoimentService $appoimentService;
    private AuthsService $authsService;
    private ProfesionalRepositoryInterface $profesionalRepository;
    private ResponseManager $responseManager;
    private StorageService $storageService;


    public function __construct(AppoimentService $appoimentService,ProfesionalRepositoryInterface $profesionalRepository,
                                AuthsService $authsService,ResponseManager $responseManager,StorageService $storageService)
    {
        $this->appoimentService=$appoimentService;
        $this->authsService=$authsService;
        $this->profesionalRepository=$profesionalRepository;
        $this->responseManager=$responseManager;
        $this->storageService=$storageService;
    }

    public function getDailyAppoiments(array $request):array{
        ProfesionalRequest::validateCedula($request);
        $cedula=$request['cedula'];
        return $this->appoimentService->getDailyAppoimentsProfesional($cedula);

    }
    public function getSchedule(array $request):array{
        ProfesionalRequest::validateGetScheduleRequest($request);
        $cedula=$request['cedula'];
        $from=$request['from'];
        $to=$request['to'];

        return $this->appoimentService->getScheduleProfesional(identity:$cedula,from:$from,to:$to);
    }
    public function getProfesionalsAuth(array $request):array{
        ProfesionalRequest::validateCedula($request);
        $cedula=$request['cedula'];
        return $this->authsService->getAuthsByProfesionalced($cedula);
    }
    public function getProcediprosProfesional(string $cedula){
        return $this->responseManager->success(
            $this->profesionalRepository->getProcedipros(cedula:$cedula)
        );
        
    }
    public function searchProByName(array $request){
        $param=$request['param']??null;
        if (!$param){
            throw new BadRequestException("El nombre del terapeuta a buscar debe ser obligatorio",400);
        }

        $profesionals=$this->profesionalRepository->searchByString(param:$param);
        if(empty($profesionals)){
            throw new NotFoundException("No se encontrarion profesionales que coincidan con $param",404);
        }
        return $this->responseManager->success($profesionals);

    }
    public function updateInfoPro(UpdateProDto $dto, ?UploadedFile $image){
        
        if($image){
            $response=$this->storageService->uploadFile($image);
            $url=$response['data']['url']??null;
            $dto->setUrlAvatar($url);
        }
        $cedula=$dto->getUser();
        $this->profesionalRepository->updatePro(cedula:$cedula,dto:$dto);
        return $this->profesionalRepository->getProfesionalByIdentity($cedula);
        
    }

    
}