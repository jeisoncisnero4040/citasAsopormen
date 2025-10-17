<?php

namespace App\Services;

use App\Constants\AuditTemplates;
use App\Dtos\ABAEvoDto;
use App\Dtos\BasicEvoDto;
use App\Dtos\PsicoEvoDto;
use App\Events\AuditEvent;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Interfaces\AppoimentsRepositoryInterface;
use App\Interfaces\ProcediproRepositoryInterface;
use App\Mappers\AppoimentGrouper;
use App\Mappers\AppoimentMapper;
use App\Requests\AppoimentsRequests;
use App\Utils\DateManager;
use App\Utils\ResponseManager;
use Carbon\Carbon;


class AppoimentService{
    private AppoimentsRepositoryInterface $appoimentRepository;
    private ResponseManager $responseManager;
    private ProcediproRepositoryInterface $procediproRepository;
    private CacheService $cache;

    public function __construct(
        AppoimentsRepositoryInterface $appoimentRepository,
        ResponseManager $responseManager,
        ProcediproRepositoryInterface $procediproRepository,
        CacheService $cache
    )
    {
        $this->appoimentRepository=$appoimentRepository;
        $this->responseManager=$responseManager;
        $this->procediproRepository=$procediproRepository;
        $this->cache=$cache;
    }

    public function getDailyAppoimentsProfesional($identity){
        $appoiments=$this->appoimentRepository->getDailyAppoimetsProfesionalByIdentity($identity);
        $appoimentsGropued=AppoimentGrouper::groupAppoiments(($appoiments));
        return $this->responseManager->success($appoimentsGropued);
    }
    public function getScheduleProfesional(string $identity,string $from,string $to):array{
        $from=Carbon::parse($from)->format('Y-m-d');
        $to=Carbon::parse($to)->format('Y-m-d');
        $appoiments=$this->appoimentRepository->getScheduleProfesionalInRangeTime(identityNumber:$identity,from:$from,to:$to,toModel:false);
        $appoimentsGropued=AppoimentGrouper::groupAppoiments(($appoiments));
        return $this->responseManager->success($appoimentsGropued);
    }
    public function cancelAppoiments($request){
        AppoimentsRequests::validateCancelAppoiments($request);
        $ids=$request['ids'];
        $razon=$request['razon'];
        $profesional=$request['profesional'];
        $dateAppoiment=Carbon::parse($request['fecha_cita'])->format('Y-m-d H:i:s');
        $meanCancel=$request['meanCancel'];
        
        $idsCanceled=$this->appoimentRepository->cancelAppoiment(
            ids:$ids,
            meanCancel:$meanCancel,
            dateAppoiment:$dateAppoiment,
            razon:$razon
        );
        $appoimentsCanceled=$this->appoimentRepository->getAppoimentsById($idsCanceled);
        $auditMessage = str_replace(
            ['{{nombre}}', '{{ids}}', '{{usuario}}', '{{fecha}}'],
            [
                $profesional,
                str_replace('|||', ', ', $ids),
                $appoimentsCanceled[0]->usuario ?? 'desconocido',
                DateManager::nowInLargeFormat()
            ],
            AuditTemplates::CANCEL_APPOIMENTS
        );
        event(new AuditEvent($auditMessage));
        return $this->responseManager->success($appoimentsCanceled);

    }

    public function getDispoAppo(int $id){
        $infoDispo= $this->appoimentRepository->getDisponibilityAppo(id:$id);
        return $this->responseManager->success($infoDispo->toArray());
    }

    public function saveEvoFono(BasicEvoDto $evoInDto): array {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $ids = $evoInDto->getIdsToEvo();

        [$infoAppo, $procedipro, $infoDisp, $idHistoricoDx] = $this->prepareEvoData($ids);

        $evo = AppoimentMapper::evoInDtoToEVO(
            evoDto: $evoInDto,
            appoInfo: $infoAppo,
            procedipro: $procedipro,
            now: $now
        );

        $this->appoimentRepository->evoFono(
            evo: $evo, dispo: $infoDisp, ids: $ids, idHistoricoDx: $idHistoricoDx
        );

        return $this->finalizeEvo($ids, $evo, $infoAppo);
    }

    public function evoPsico(PsicoEvoDto $evoInDto): array {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $ids = $evoInDto->getIdsToEvo();
        [$infoAppo, $procedipro, $infoDisp, $idHistoricoDx] = $this->prepareEvoData($ids);

        $userHist = $infoAppo->getHistory();
        $numEvoUser = $this->appoimentRepository->getNumEvoPsicologyByHistory(history:$userHist);

        $evo = AppoimentMapper::evoPsicoEvoInDtoToEvo(
            evoDto: $evoInDto,
            appoInfo: $infoAppo,
            procedipro: $procedipro,
            numEvoModel: $numEvoUser,
            now: $now
        );

        $this->appoimentRepository->evoPsico(
            evo: $evo, dispo: $infoDisp, ids: $ids, idHistoricoDx: $idHistoricoDx
        );

        return $this->finalizeEvo($ids, $evo, $infoAppo);
    }
    public function evoAba(ABAEvoDto $evoInDto):array{
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $ids = $evoInDto->getIdsToEvo();
        [$infoAppo, $procedipro, $infoDisp, $idHistoricoDx] = $this->prepareEvoData($ids);

        $userHist = $infoAppo->getHistory();
        $numEvoUser = $this->appoimentRepository->getNumEvoPsicologyByHistory(history:$userHist);

        $evo = AppoimentMapper::evoABAEvoInDtoToEvo(
            evoDto: $evoInDto,
            appoInfo: $infoAppo,
            procedipro: $procedipro,
            numEvoModel: $numEvoUser,
            now: $now
        );

        $this->appoimentRepository->evoABA(
            evo: $evo, dispo: $infoDisp, ids: $ids, idHistoricoDx: $idHistoricoDx
        );

        return $this->finalizeEvo($ids, $evo, $infoAppo);
        
    }


    public function getHistDxByAppoId(int $id){
        $histDx=$this->appoimentRepository->getDxHistoryByAppoId(id:$id);
        return $this->responseManager->success(
            $histDx?$histDx->toArray():null
        );
    }
    public function activePastAppos(array $request){
        $ids=$request['ids']??[];
        if(empty($ids)||!$ids){
            throw new BadRequestException("No se han seleccionado citas para Actualizar",400);
        }
        $this->appoimentRepository->openPastApposByIds(ids:$ids);
        return $this->responseManager->success(
            $this->appoimentRepository->getAppoimentsById($ids)
        );
        
    }
    private function prepareEvoData(array $ids): array {
        if (empty($ids)) {
            throw new BadRequestException("No se han seleccionado citas a evolucionar",400);
        }

        $firstId = (int)$ids[0];
        $infoAppo = $this->appoimentRepository->getInfoFacAppoiment($firstId);
        $codProcedipro = $infoAppo->getCodProcedipro();
        $idHistoricoDx = $infoAppo->getHistoricoDxId();
        $procedipro = $this->procediproRepository->getProcediproByCod((int) $codProcedipro);

        $infoDisp = $this->appoimentRepository->getDisponibilityAppo(id:$firstId);
        if (!$infoDisp->isAvaible()) {
            throw new BadRequestException("La orden seleccionada no tiene disponibilidad para marcar asistencia",400);
        }

        return [$infoAppo, $procedipro, $infoDisp, $idHistoricoDx];
    }
    private function finalizeEvo(array $ids, $evo, $infoAppo): array {
        $apposEvo = $this->appoimentRepository->getAppoimentsById($ids);
        $this->cache->add(
            key: 'evo:' . $evo->getCedula() . ':' . $evo->getHistory() . ':' . $evo->getAutoriz(),
            data: $evo->toBufferArray()
        );

        $msmUdit = AuditTemplates::renderEvoBasicAuditMsm(
            nombre: $evo->getProfesional(),
            ids: implode(',', $ids),
            usuario: $infoAppo->getClient()
        );
        event(new AuditEvent($msmUdit));

        return $this->responseManager->success($apposEvo);
    }

}   