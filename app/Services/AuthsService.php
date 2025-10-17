<?php
namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\AuthsInterface;
use App\Requests\AuthsRequets;
use App\Utils\ResponseManager;
use App\Events\AuditEvent;
use App\Constants\AuditTemplates;
use App\Interfaces\AppoimentsRepositoryInterface;
use App\Utils\DateManager;

class AuthsService {
    private AuthsInterface $authsRepository;
    private ResponseManager $responseManager;
    private AppoimentsRepositoryInterface $appoimentsRepository;

    public function __construct(AuthsInterface $authsRepository,
                                ResponseManager $responseManager,
                                AppoimentsRepositoryInterface $appoimentsRepository)
    {
        $this->authsRepository=$authsRepository;
        $this->responseManager=$responseManager;
        $this->appoimentsRepository=$appoimentsRepository;
    }

    public function getAuthsByProfesionalced(string $cedProfesional){
        $auths=$this->authsRepository->getProfesionalsAuths($cedProfesional);
        if(empty($auths)){throw new NotFoundException("El profesional no registra autorizaciones activas",404);}
        return $this->responseManager->success($auths);
    }
    public function getAuthsByIds($request){
        $ids=$request['ids'];
        $idsArray=explode(',',$ids);
        $auths=$this->authsRepository->getAuthsByIds($idsArray);
        if(empty($auths)){throw new NotFoundException("El profesional no registra autorizaciones activas",404);}
        return $this->responseManager->success($auths);

    }
    public function closeAuth(array $request)
    {
        AuthsRequets::validateDataToCloseAuth(data: $request);
        $cedulaProf=$request['cedula'];
        $codent=$request['codent'];
        $history=$request['historia'];
        $autoriz=$request['autorizacion'];
        $procedure=$request['procedimiento'];

        $idsToDelete=$this->appoimentsRepository->getIdsAppoimentsByAutoriz(
                                            autoriz:$autoriz,
                                            cedulaProfesional:$cedulaProf,
                                            codEnt:$codent,
                                            history:$history,
                                            procedure:$procedure
                                        );
        
        $idsInArray = collect($idsToDelete)->pluck('id')->toArray(); 
        $id = (int) $request['id'];
        $razon=$request['razon'];
        $profesionalName=$request['profesional'];
        $this->authsRepository->closeAuth(razon:$razon,
                                        profesional:$profesionalName,
                                        idAutoriz:$id,
                                        ids:$idsInArray);
        $authsClosed = $this->getAuthsByIds(['ids' => $id]);
        $usuario = $authsClosed[0]->usuario ?? 'Desconocido';

        $messageToAudit = str_replace(
            AuditTemplates::VARS_CLOSE_AUTH,
            [
                $request['profesional'],
                $request['autorizacion'],
                $id,
                implode(',',$idsInArray),
                $usuario,
                DateManager::nowInLargeFormat()
            ],
            AuditTemplates::CLOSE_AUTH
        );

        event(new AuditEvent(auditMessage: $messageToAudit));

        return $authsClosed;
    }

}