<?php

namespace App\Services;
use App\Interfaces\PqrRepositoryInterface;
use App\Services\BaseService;
use App\Utils\ResponseManager;
use App\Requests\PqrValidator;
use App\Utils\UrlEncoder;
use App\Events\NotifyPqrsEvent;
use App\Events\RegisterLogPqrsSendedAreaEvent;
use App\Services\StorageService;
use App\Services\EmailService;
use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Http\UploadedFile;


class PqrService extends BaseService{
    protected ResponseManager $responseManager;
    private PqrRepositoryInterface $pqrRepository;
    private StorageService $storageService;
    private EmailService $emailService;
    private string $tag;

    public function __construct(ResponseManager $responseManager,PqrRepositoryInterface $pqrRepository,StorageService $storageService,EmailService $emailService) {
        $this->responseManager=$responseManager;
        $this->pqrRepository=$pqrRepository;
        $this->storageService=$storageService;
        $this->emailService=$emailService;
        $this->tag='pqr(s)';
    }
    public function createPqr(array $request):array
    {
        PqrValidator::validateNewPqrRequest(newPqrData:$request);
        $idNewPqr=$this->pqrRepository->create(newPqrData:$request);
        $pqr=$this->pqrRepository->find(pqrId:$idNewPqr);
        return $this->responseManager->created($pqr);
    }
    public function findPqrById(int $pqrId,array $request):array
    {
        $statusPqr=$request['estado']??null;
        $pqr=$this->pqrRepository->find(pqrId:$pqrId,estado:$statusPqr);
        return $this->driveResponsePersistence($pqr,$this->tag);
    }
    public function getAllPqrs(array $request):array
    {
        $statusPqr=$request['estado']??null;
        $pqr=$this->pqrRepository->getPqrs(estado:$statusPqr);
        return $this->driveResponsePersistence($pqr,$this->tag);
    }
    public function sendAreaNotification(array $pqrs,?string $token):array
    {   
        
        $id=(int)$pqrs['id'];
        $urlForm=UrlEncoder::getHashIdAttribute($id);
        $pqrs['url_form']=$urlForm;
        event(new NotifyPqrsEvent($pqrs));
        $pqrsUpdated=$this->pqrRepository->remitPqrsToArea($id);
        $this->ensureRowUpdated($pqrsUpdated,$this->tag);
        event(new RegisterLogPqrsSendedAreaEvent($pqrs,$token));
        return $this->findPqrById($id,[]);

    }
    public function saveAnswerArea(array $request, array $actions, array $files)
    {   
        PqrValidator::validateDataToSaveAnswersPqrsFromArea($request);
        $idEncoded=$request['id'];
        $id=$this->idHasheToIdInteger($idEncoded);
        unset($request['id']);
        unset($request['actions']);
        $actionsWithUrls = $this->uploadEvidenciesToStorage($actions, $files);
        $pqrsUpdated = $this->pqrRepository->saveAnswerAreaPqrs($request,$actionsWithUrls,$id);
        $this->ensureRowUpdated($pqrsUpdated,$this->tag);
        return $this->findPqrById((int) $id,[]);
    }
    public function getCtionsDataPqrs($pqrsId){
        $actions=$this->pqrRepository->getActionsPqrs($pqrsId);
        return $this->driveResponsePersistence($actions,"acciones");
    }
    public function getPqrsByIdHashed($pqrsIdHashed){
        $id=$this->idHasheToIdInteger($pqrsIdHashed);
        return $this->findPqrById($id,[]);
    }
    public function changeAreaPqrs($idPqrs, $newDataPqrs)
    {   
        PqrValidator::ValidateDataToChangeAreaPqrs($newDataPqrs);
        $urlsToDelete = $this->pqrRepository->changueAreaPqrs($idPqrs, $newDataPqrs);
        $this->storageService->deleteFiles($urlsToDelete);
        return $this->findPqrById($idPqrs,[]);
    }
    public function saveAnswerToUser(UploadedFile $pdf, array $request, mixed $adjunts)
    {   
        if(!is_array($adjunts)){
            $adjunts=[$adjunts];
        }
        $id=(int)$request['id'];
        $responseStorage = $this->storageService->uploadEvidencie($pdf, 'documentos');
        $url=$responseStorage['data']['url']??'No Registra';
        $this->emailService->answerPqrsClient($request,$pdf,$adjunts);
        $update=$this->pqrRepository->saveAnswerToUser($id,['url_respuesta'=>$url]);
        return $this->findPqrById($id,[]);
    }
    public function closePqrs($id){
        $pqrsClosed=$this->pqrRepository->closePqrs($id);
        return $this->findPqrById($id,[]);
    }
    private function idHasheToIdInteger($idEncoded){
        
        $id = UrlEncoder::getIdFromHasing($idEncoded);
        if(empty($id)){
            throw new BadRequestException("El formulario introducido no es valido",400);
        }
        return  $id[0];
    }
    
    private function uploadEvidenciesToStorage(array $actions, array $files): array
    {
        foreach ($actions as $index => &$action) {
            $evidenceFile = $files[$index]['evidence'] ?? null;
            if ($evidenceFile) {
                $responseStorage= $this->storageService->uploadEvidencie($evidenceFile);
                $url=$responseStorage['data']['url']??'No Registra';
                $action['url_evidencia'] = $url;
                if (file_exists($evidenceFile->getRealPath())) {
                    unlink($evidenceFile->getRealPath());
                }
                unset($action['evidence']);
            }
        }
        return $actions;
    }
    
    
}