<?php
namespace App\Services;

use App\contracts\Sendeable;
use App\Validators\ChatValidator;

use App\Services\TwilioService;
use App\utils\ResponseManager;
use App\utils\WhatsappTemplates;
use Carbon\Carbon;

 

class WhatsappService implements Sendeable  {
    private $responseManager;
    private $twilioService;
    private $apiCitasAsopormenService;
    private $citasService;
    const TEST_PHONE_NUMBER ='3222551222';
    const TEXT_CONFIRM_CITA='confirmar';
    const TEXT_CANCEL_CITA='cancelar';


    public function __construct(ResponseManager $responseManager, TwilioService $twilioService,
                                ApiCitasAsopormenService $apiCitasAsopormenService,
                                CitasService $citasService)
    {
        $this->responseManager = $responseManager;
        $this->twilioService = $twilioService;
        $this->apiCitasAsopormenService=$apiCitasAsopormenService;
        $this->citasService=$citasService;
    }

    public function startChat($request)
    {    
        $this->validateStartChatData($request);  
        $citaSaved = $this->saveCita($request);
    
        if ($citaSaved['status'] == 201) {
            $dataMessageSended = $this->twilioService->sendWhatsAppTemplate($request,$citaSaved['data']['id']);  
            return $this->responseManager->success($dataMessageSended);
        }
    }
    public function sendFailedMessage($request){
        ChatValidator::failedMsmValidate(($request));  
        $dataMessageSended = $this->twilioService->sendErrorSystemTemplate($request); 
        return $this->responseManager->success($dataMessageSended); 
        
    }
    public function handleClientMessage($request){
        $numberClient = $request['From'];
        $numberClientCleaned = $this->cleanTelephoneNumber($numberClient);
        $message=$this->getMessageToResponse($request,$numberClientCleaned);
        $dataMessageSended = $this->sendMessage($message, $numberClient); 
        return $this->responseManager->success($dataMessageSended);


    }

    public function sendMessageRetrievePassword($request){
        ChatValidator::RetrievePasswordValidate($request);
        $dataMessageSended = $this->twilioService->sendForgotPasswordTemplate($request); 
        return $this->responseManager->success($dataMessageSended); 

    }
    public function sendConfirmationProgramedOrdenMessage($request){
        ChatValidator::validateDataToConfirmProgramationOrden($request);
        $dataMessageSended=$this->twilioService->sendConfirmationMessage($request);
        return $this->responseManager->success($dataMessageSended); 

    }
    
    private function getMessageToResponse($request, $numberClientCleaned) {
        $citaBynum = $this->getCitaByThelephoneNumber($numberClientCleaned);
    
        if (!$citaBynum) {
            return WhatsappTemplates::citasOnWaitNotFound();
        }
        $id =$this->extractButtonIdFromPayload($request);
        
        if ($this->isLaterMessageInterative($id,$citaBynum)) {
            return WhatsappTemplates::citaOutRangeTimeTemplate();
        }
    

        $text = strtolower($request['Body']);
        switch ($text) {
            case self::TEXT_CONFIRM_CITA:
                $citaAlreadyConfirmaded=$this->isCitaCanceled($citaBynum);
                if ($citaAlreadyConfirmaded){
                    return WhatsappTemplates::CitaAlreadyCanceled();
                }
                return $this->confirCita($citaBynum);
    
            case self::TEXT_CANCEL_CITA:
                return $this->sendCitaToWait($citaBynum);
    
            default:

                $existsCitaOnWait = $this->checkCitaOnWait($numberClientCleaned, $citaBynum);
                $citaOnTime = $this->checkCitaOnTime($citaBynum);
    

                if ($existsCitaOnWait && $citaOnTime) {
                    return $this->cancelCita($citaBynum, $text);
                }
    

                return !$existsCitaOnWait 
                    ? WhatsappTemplates::citasOnWaitNotFound() 
                    : WhatsappTemplates::citaOutRangeTimeTemplate();
        }
    }
    
    private function validateStartChatData($request)
    {
        ChatValidator::startChatValidateRequest($request);
    }

    public function sendMessage($message, $number,$type='onlyText')
    {   
   
        $appInLocal=env('APP_ENV')=='local';
        $telephone = $appInLocal ? self::TEST_PHONE_NUMBER : $number;
        return $this->twilioService->sendWhatsAppMessage($number, $message,$type);  
    }

    private function getCitaByThelephoneNumber($telephone){
        return $this->citasService->findLaterCitaAvaiableByTelephoneNumber($telephone);
    }
    private function extractButtonIdFromPayload($request) {
       
        if (isset($request['ButtonPayload']) && str_contains($request['ButtonPayload'], '|||')) {
            $payloadParts = explode('|||', $request['ButtonPayload']);
            return $payloadParts[1] ?? null;
        }
    
        return null; 
    }
    private function isLaterMessageInterative($id, $citaBynum) {

        return $id && (int)$id !== (int)$citaBynum->id;
    }
    private function checkCitaOnWait($telephone,$cita){
        return $this->citasService->verifyCitaOnWaitMode($telephone,$cita);
    }
    private function markCitasAsWorked($cita, $action) {
        return $this->citasService->updateCita($cita,$action);
    }
    
    private function checkCitaOnTime($cita){
        return $this->citasService->verifyCitaUpdateFiveMinsBefore($cita);
    }
    private function saveCita($request){
        return $this->citasService->sendCitaToWait($request);
    }
    private function cleanTelephoneNumber($telephone)
    {
         
        if (strpos($telephone, 'whatsapp:+57') === 0) {
            return str_replace('whatsapp:+57', '', $telephone);
        }
        return $telephone;
    }
    
    private function confirCita($cita){
        $payload=$this->buildPayloadToApiCitasRequest($cita);
        $citaConfirmate=$this->apiCitasAsopormenService->sendRequestToConfirmCita($payload);
        if(!$citaConfirmate){
            return WhatsappTemplates::serverErrorTemplate();
        }
        $this->markCitasAsWorked($cita, 'accepted');
        return WhatsappTemplates::ObservationTemplate($cita->observations);
    }
    private function sendCitaToWait($cita){
        $this->markCitasAsWorked($cita, 'waiting');
        return WhatsappTemplates::SendCitaToWait();
    }
    public function cancelCita($cita,$razon){
        $payload=$this->buildPayloadToApiCitasRequest($cita,$razon);
        $citaCanceled=$this->apiCitasAsopormenService->sendRequestToCancelCita($payload);
        if(!$citaCanceled){
            return WhatsappTemplates::serverErrorTemplate();
        }
        $this->markCitasAsWorked($cita,'canceled');
        return WhatsappTemplates::citaCanceledMessage();
    }
    private function isCitaCanceled($cita)
    {
        return (bool) $cita->canceled;
    }
    
    private function buildPayloadToApiCitasRequest($cita, $razon = null) {
        $payload = [
            'ids' =>$cita->cita_ids,
            'fecha_cita'=>Carbon::now()->format('Y-m-d H:i')
        ];
    
        if (!empty($razon)) {
            $payload['razon'] = $razon;
            $payload['meanCancel']='whatsapp';
        }
    
        return $payload;
    }
    
    
}
