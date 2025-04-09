<?php
namespace App\Services;

use App\Exceptions\CustomExceptions\NotFoundException;
use App\Mappers\OrdersCaseMapper;
use App\Utils\ResponseManager;
use App\Models\CaseOrderModel;
use App\Requests\OrdersCaseValidator;
use Illuminate\Support\Str;


class OrdersCaseService{
    private $caseOrderModel;
    private $responseManager;

    public function __construct(CaseOrderModel $caseOrderModel, ResponseManager $responseManager)
    {
        $this->responseManager=$responseManager;
        $this->caseOrderModel=$caseOrderModel;

    }
    public function newCase($orderCaseInDto){
        OrdersCaseValidator::validateNewCase($orderCaseInDto);
        $eps=$orderCaseInDto['eps_cliente'];
        if(isset($orderCaseInDto['url_case_in_pdf'])){
            $case=$this->caseOrderModel::CreateCaseWithPdf($orderCaseInDto);
            return $this->responseManager->created($case);
        }
        if (Str::contains($eps, 'particular', true)) {
            $case = $this->caseOrderModel::CreateCaseParticularUser($orderCaseInDto);
            return $this->responseManager->created($case);
        }
        $case=$this->caseOrderModel::create($orderCaseInDto);
        return $this->responseManager->created($case);

    }
    public function getAllCasosAvaiables(){
        $cases=$this->caseOrderModel::getAllUnfinishedCases();
        if(empty($cases)){
            throw new NotFoundException("No se han encontrado casos de Ordenes disponibles",404);
        }
        return $this->responseManager->created($cases);
    }
    public function getCaseById(int $id){
        $case=$this->caseOrderModel::getCaseById($id);
        if(empty($case)){
            throw new NotFoundException("Caso no encontrado",404);
        }
        return $this->responseManager->created($case);
    }
    public function acceptCase($dataToAcceptCaseInDto){
        OrdersCaseValidator::validateAcceptCase($dataToAcceptCaseInDto);
        $dataToAcceptCase=OrdersCaseMapper::DataFromAcceptOrderInDtoToData($dataToAcceptCaseInDto);
        $caseAccepted=$this->caseOrderModel->acceptCase($dataToAcceptCase);

        /**enviar evento para notificar por whatsapp */
        return $this->responseManager->success($caseAccepted);
  
    }
    public function rejectCase ($dataToRejectCaseInDto){
        OrdersCaseValidator::validateRejectCase($dataToRejectCaseInDto);
        $dataToRejectCase=OrdersCaseMapper::DataFromRejectInDtoToData($dataToRejectCaseInDto);
        $caseRejected=$this->caseOrderModel->rejectCase($dataToRejectCase);
        return $this->responseManager->success($caseRejected);
    }
    public function closedCase($dataToFinishCaseInDto){
        OrdersCaseValidator::validateCloseCase($dataToFinishCaseInDto);
        $dataToFinish=OrdersCaseMapper::DataFromCloseInDtoToData($dataToFinishCaseInDto);
        $caseClosed=$this->caseOrderModel->finishCase($dataToFinish);
        return $this->responseManager->success($caseClosed);

    }
    public function searchCasesByClient($dataClient){
        OrdersCaseValidator::CheckDataClientToSearchCases($dataClient);
        $hasCodigo = !empty($dataClient['codigo']);
        $orders = $hasCodigo 
            ? $this->caseOrderModel->getCasesByCodigo($dataClient)
            : $this->caseOrderModel->getCasesByIdAndCedula($dataClient);
        if(empty($orders)){
            throw new NotFoundException("El no registra ordenes cargadas",404);
        }
        return $this->responseManager->success($orders);
    }
    
}