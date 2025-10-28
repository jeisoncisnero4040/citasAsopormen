<?php

namespace App\ChatHandlers;

use App\Services\CitasService;
use App\Services\ChatsService;
use App\Services\CaseOrderService;
use App\Events\MessageSentEvent;
use App\Utils\WhatsappTemplates;
use App\Jobs\ProcessImageJob;
use App\Jobs\ProcessPdfJob;
use App\Constants\StatusChatConstans;
use App\Events\MessageInterativeEvent;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Str;

class UploadOrder {
    private float $status;
    private string $numCel;
    private ?string $text;
    private ?string $buttonPayload;
    private ?string $urlMedia;
    private ?string $messageType;
    private CitasService $citasService;
    private ChatsService $chatsService;
    private CaseOrderService $caseOrderService;  

    public function __construct(
        float $status, 
        string $numCel, 
        ?string $text, 
        ?string $buttonPayload, 
        ?string $urlMedia,
        ?string $messageType, 
        CitasService $citasService, 
        ChatsService $chatsService,
        CaseOrderService $caseOrderService
    ) {
        $this->status = $status;
        $this->numCel = $numCel;
        $this->text = $text;
        $this->buttonPayload = $buttonPayload;
        $this->urlMedia = $urlMedia;
        $this->messageType = $messageType;
        $this->citasService = $citasService;
        $this->chatsService = $chatsService;
        $this->caseOrderService=$caseOrderService;

        $chat = $this->chatsService->getChatByNumCel($this->numCel);
        $this->chatData=$chat['data'] ?? [];
        
    }

    public function uploadOrderDriver() {
        switch ($this->status) {
            case StatusChatConstans::INDEXUPLOADORDER:
                return $this->handleOptionSelected();
            case StatusChatConstans::HANDLEFRONTCC:
                return $this->handleImageFrontCc();
            case StatusChatConstans::HANDLEBACKCC:
                return $this->handleImageBackCc();    
            case StatusChatConstans::HANDLEORDERMEDICAL:
                return $this->handleImageOrderMedical();
            case StatusChatConstans::HANDLEAUTORIZ:
                return $this->handleAutoriz();
            case statusChatConstans::HANDLECLINICALHISTORY:
                return $this->handleImageClinicalHistory();
            case statusChatConstans::HANDLEPREAUTORIZ:
                return $this->handlePreAutoriz();
            case statusChatConstans::HANDLEOBSERVATIONS:
                return $this->handleObservations();
            case statusChatConstans::QUESTIONPARTICULARUSERHAVEHISTORYCLINICAL:
                return $this->handleUserCanClinicalHistory();
            case StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER:
                return $this->handleDetailsOrder();
            case StatusChatConstans::HANDLEORDERDATAINPDF:
                return $this->handleOrderDataInPdf();

            
        }
    }
    private function handleOptionSelected(){
        if (strtolower($this->buttonPayload) != 'atras') {
            $this->addkeyToChat(['mean_upload_order'=>$this->buttonPayload]);
            $this->sendMessage(WhatsappTemplates::generateUploadCcImageTemplate(),StatusChatConstans::HANDLEFRONTCC);
            return;
        }
        $client=$this->chatData['data_user']['nombre']??'Usuario';
        $this->sendInterativeMessage(StatusChatConstans::INDEXCLOSEDCHAT,StatusChatConstans::MENUSTATUS,$client);
    }

    private function handleImageFrontCc() {
        
        if ($this->checkIsOnlyImage()) {
            $this->createProcessImageJob(StatusChatConstans::HANDLEFRONTCC,StatusChatConstans::HANDLEBACKCC,WhatsappTemplates::generateUploadBackCcImageTemplate(),'front_cedula');
            return;
        }
        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(),StatusChatConstans::HANDLEFRONTCC);
    }
    
    private function handleImageBackCc() {
        $nextStatus=$this->getStatusLaterBackCedula();
        $nextTemplate=$this->getTemplateLaterBackCedula($nextStatus);
        if ($this->checkIsOnlyImage() && $nextTemplate) {
            $this->createProcessImageJob(StatusChatConstans::HANDLEBACKCC,$nextStatus,$nextTemplate,'back_cedula');
            return;
        }elseif ($this->checkIsOnlyImage() && !$nextTemplate) {
            $this->createProcessImageJobWhithInterativeMessage(StatusChatConstans::QUESTIONPARTICULARUSERHAVEHISTORYCLINICAL,'back_cedula');
            return;
        }

        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(),StatusChatConstans::HANDLEBACKCC);
    }
    private function handleUserCanClinicalHistory(){
        $userCanHistoryClinical=$this->OnlyPayloadButtonDidSelected("si");
        if($userCanHistoryClinical){
            $this->sendMessage(WhatsappTemplates::startClinicalHistory(),StatusChatConstans::HANDLECLINICALHISTORY);
        }else{
            $this->sendMessage( WhatsappTemplates::generateUploadServiceDetailsTemplate(),StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER);
        }
    }
    
    private function handleImageOrderMedical() {
        if ($this->checkIsOnlyImage()) {
            $this->createProcessImageJob(StatusChatConstans::HANDLEORDERMEDICAL,StatusChatConstans::HANDLECLINICALHISTORY,WhatsappTemplates::startClinicalHistory(),'order');
            return;
        }
        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(),StatusChatConstans::HANDLEORDERMEDICAL);
    
    }
    private function handleImageClinicalHistory() {
        if ($this->OnlyPayloadButtonDidSelected('salir')) {
            // Sale del bucle de subir imágenes
            
            $nextStatus = $this->isEpsUser('nueva')? 
                          StatusChatConstans::HANDLEPREAUTORIZ :($this->isEpsUser('particular') ?StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER:StatusChatConstans::HANDLEAUTORIZ ) ;
                          
            if ($nextStatus == StatusChatConstans::HANDLEPREAUTORIZ) {
                $nextTemplate = WhatsappTemplates::generateUploadPreautorizNuevaEpsTemplate();
                $this->sendMessage($nextTemplate, $nextStatus);
            } elseif($nextStatus == StatusChatConstans::HANDLEAUTORIZ) {    
                $nextTemplate = $this->isEpsUser('sanitas')?WhatsappTemplates::requestAutorizationToSanitasUser():WhatsappTemplates::generateUploadAutorizTemplateTemplate();
                $this->sendMessage($nextTemplate, $nextStatus);
            }elseif($nextStatus == StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER){
                $nextTemplate = WhatsappTemplates::generateUploadServiceDetailsTemplate();
                $this->sendMessage($nextTemplate, $nextStatus);
            }
            return;
        }
    
        if ($this->checkIsOnlyImage()) {
            $this->createProcessImageJobWhithInterativeMessage(StatusChatConstans::HANDLECLINICALHISTORY, 'clinical_history');
            return;
        }
    
        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(), StatusChatConstans::HANDLECLINICALHISTORY);
    }
    
    private function handlePreAutoriz() {
        $nextTemplate = $this->isEpsUser("sanitas")?WhatsappTemplates::requestAutorizationToSanitasUser():WhatsappTemplates::generateUploadAutorizTemplateTemplate();
        if ($this->checkIsOnlyImage()) {
            $this->createProcessImageJob(StatusChatConstans::HANDLEPREAUTORIZ,StatusChatConstans::HANDLEAUTORIZ,$nextTemplate,'preautoriz');
            return;
        }
        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(), StatusChatConstans::HANDLEPREAUTORIZ);
    }
    private function handleAutoriz(){
        $isSanitasUser =$this->isEpsUser("sanitas");
    
        if ($this->isOnlyImageOrIsOnlyText()) {
            if ($this->text && $isSanitasUser) {
                $this->updateKeyToChat('order_data', ["autoriz_code" => $this->text]);
                $this->sendInterativeMessage(StatusChatConstans::HANDLEOBSERVATIONS, StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER, null);
                return;
            } elseif ($this->urlMedia) {
                $this->createProcessImageJobWhithInterativeMessage(StatusChatConstans::HANDLEOBSERVATIONS, 'autoriz');
                return;
            }
        } 
        
        $this->sendMessage(WhatsappTemplates::failedUploadImageTemplate(), StatusChatConstans::HANDLEORDERMEDICAL);
    }
    private function handleDetailsOrder(){
        $this->updateKeyToChat('order_data', ['details_particular_user' => $this->text]);
        $this->sendInterativeMessage(StatusChatConstans::HANDLESERVICEDETAILSTOREQUESTUSER,statusChatConstans::HANDLEOBSERVATIONS,null);
        return;
    }
    private function handleObservations() {
        $observations=$this->OnlyPayloadButtonDidSelected("no")?null:$this->text;
        $this->updateKeyToChat('order_data', ['observations' => $observations]);
        $caseCod=$this->getCodCaseOrder();

        $msm=$caseCod?WhatsappTemplates::sendCodCasoCreated($caseCod):"Error al crear la orden";
        $this->sendInterativeMessage(2.75,StatusChatConstans::INDEXCLOSEDCHAT,$msm);
    }
    private function handleOrderDataInpdf(){
        if(!$this->checkIsOnlyPdf()){
            $this->sendMessage("El mensaje introducido no es un documento de texto, por favor intenta denuevo",StatusChatConstans::HANDLEORDERDATAINPDF);
        }

        $this->createProcessPdfJobWhithInterativeMessage(StatusChatConstans::HANDLEOBSERVATIONS,'pdf');
        return;
                                                                                                                                                                            
    }
    private function checkIsOnlyPdf(){
        return !$this->text && $this->urlMedia && $this->messageType === 'document';
    }

    private function checkIsOnlyImage() {
        return !$this->text && $this->urlMedia && $this->messageType === 'image';
    }
    private function isOnlyImageOrIsOnlyText(){
        $epsUser=$this->chatData['data_user']['entidad']??null;
        $sanitasUser = Str::contains(strtolower($epsUser),"sanitas");
        if(!$sanitasUser){
            return $this->checkIsOnlyImage();
        }
        return !$this->text && $this->urlMedia && $this->messageType === 'image' || $this->text && !$this->urlMedia;
        
    }

    private function getKeyToChat(string $key) {
        $response = $this->chatsService->getChatByNumCel($this->numCel);
        $chat = $response['data'] ?? [];

        return $chat[$key] ?? null;
    }

    private function updateKeyToChat(string $key, array $newData) {
        $chatData = $this->getKeyToChat($key) ?? [];

         
        $updatedData = array_merge($chatData, $newData);

        $this->chatsService->updateKeyInChat($this->numCel, $key, $updatedData);
    }
    private function sendMessage($text,$nextStatus){
        event(new MessageSentEvent($this->numCel, $text,$nextStatus));
    }
    private function sendInterativeMessage(float $currentStatus,float $nextStatus,mixed $data){
        event(new MessageInterativeEvent($data,$currentStatus,$nextStatus,$this->numCel)); 

    }
    private function createProcessImageJob(float $currentStatus, float $nextStatus,string $nextTemplate,string $keyChatToUpdate){
        ProcessImageJob::dispatch($this->urlMedia,$this->numCel,$currentStatus,$nextStatus,$nextTemplate,$keyChatToUpdate)->onQueue('imagenes');
        $this->sendMessage("Se esta procesando su solicitud, por favor espere en linea",StatusChatConstans::ESPECIALSTATUSONWHATING);
    }
    private function addkeyToChat(array $newData){
        $this->chatsService->addKeysToChat($this->numCel,$newData);
    }
    private function canUserUploadNewImage() {
        $images = $this->chatData['data']['order_data']['clinical_history'] ?? [];
        if (!is_array($images)) {
            return true; 
        }
        return count($images)<=4;
    }
    private function createProcessImageJobWhithInterativeMessage(float $currentStatus,string $keyChatToUpdate){
        ProcessImageJob::dispatch($this->urlMedia,$this->numCel,$currentStatus,$currentStatus,null,$keyChatToUpdate)->onQueue('imagenes');
        $this->sendMessage("Se está procesando su solicitud, por favor espere en línea", StatusChatConstans::ESPECIALSTATUSONWHATING);
    }
    private function createProcessPdfJobWhithInterativeMessage(float $currentStatus,string $keyChatToUpdate){
        ProcessPdfJob::dispatch($this->urlMedia,$this->numCel,$currentStatus,$currentStatus,null,$keyChatToUpdate)->onQueue('imagenes');
        $this->sendMessage("Se esta procesando su solicitud, por favor espere en linea",StatusChatConstans::ESPECIALSTATUSONWHATING);
    }
    private function OnlyPayloadButtonDidSelected(string $payloadValue){
        return !$this->urlMedia && $this->buttonPayload == $payloadValue;
    }
    private function getStatusLaterBackCedula(){
        $epsUser=$this->chatData['data_user']['entidad']??null;
        $meanUploadOrder=$this->chatData['mean_upload_order']??null;
        $particularUser = Str::contains(strtolower($epsUser),"particular");
        if ($meanUploadOrder=='pdf'){
            return StatusChatConstans::HANDLEORDERDATAINPDF;
        }
        if($particularUser){
            return StatusChatConstans::QUESTIONPARTICULARUSERHAVEHISTORYCLINICAL;
        }
        return StatusChatConstans::HANDLEORDERMEDICAL;
    }
    private function getTemplateLaterBackCedula(float $status){
        switch($status){
            case StatusChatConstans::HANDLEORDERDATAINPDF:
                return WhatsappTemplates::generateUploadPdfTemplate();
            case StatusChatConstans::QUESTIONPARTICULARUSERHAVEHISTORYCLINICAL:
                return null;
            case StatusChatConstans::HANDLEORDERMEDICAL:
                return WhatsappTemplates::generateUploadMedicalOrderTemplate();
            default:
                return null;
        }
    }
    private function isEpsUser(string $eps){
        $epsUser = $this->chatData['data_user']['entidad'] ?? null;
        return Str::contains(strtolower($epsUser), $eps) ;
    }
    private function getCodCaseOrder(){
        
        $response = $this->caseOrderService->sendChatDataToOrderCase($this->numCel);
        Log::error('Respuesta del servicio de orden de caso:', $response);
        if (!isset($response['data']['id'])) {
            return null;
        }
        return $response['data']['id'];
    }
}
