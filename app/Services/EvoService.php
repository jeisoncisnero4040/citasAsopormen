<?php
namespace App\Services;

use App\Constants\AuditTemplates;
use App\Dtos\GetEvoDto;
use App\Events\AuditEvent;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Interfaces\ProcediproRepositoryInterface;
use App\Strategies\EvoStrategyFactory;
use App\Utils\ResponseManager;

class EvoService{
    private ResponseManager $responseManager;
    private ProfesionalService $profesionalService;
    private EvoStrategyFactory $evoStrategyFactory;
    private ProcediproRepositoryInterface $procediproRepository;

    public function __construct(ResponseManager $responseManager,
                                EvoStrategyFactory $evoStrategyFactory,
                                ProfesionalService $profesionalService,
                                ProcediproRepositoryInterface $procediproRepository
                                )
    {
        $this->profesionalService=$profesionalService;
        $this->responseManager=$responseManager;
        $this->evoStrategyFactory=$evoStrategyFactory;
        $this->procediproRepository=$procediproRepository;
    }
    public function getProcediprosToPrintEvo($request){
        return $this->profesionalService->getProcediprosProfesional($request['cedula']);
    }
    public function getEvo(GetEvoDto $request): array {
        $procediCode = $request->getProcedipro();
        $procedipro = $this->procediproRepository->getProcediproByCod((int) $procediCode);

        $strategy = $this->evoStrategyFactory->make(procedipro:$procedipro);
        $evolutions = $strategy->getEvolutions(dto:$request,procedipro:$procedipro);

        if(empty($evolutions)){
            throw new NotFoundException("No se Encontrarón Evoluciones de {$request->getClient()} en el rango de fechas 
                                        seleccionado con el procedimiento seleccionado",404);
        }

        $evoWithSignature = collect($evolutions)->map(function ($evo) {
            $path = $evo->getPathUrl(); 
            $url = $path ? route('firmas.serve', ['path' => $path]) : null;

            $evo->setUrl($url);

            return $evo->toArray(); 
        })->toArray();
        
        $textAudit=AuditTemplates::renderGetEvoAudit(
            nombre:$request->getProfesional(),
            client:$request->getClient(),
            from:$request->getFrom(),
            to:$request->getTo()
        );
        event(new AuditEvent(auditMessage:$textAudit));
        return $this->responseManager->success($evoWithSignature);

    }
}