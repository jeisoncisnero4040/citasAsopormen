<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use Twilio\Rest\Client;
use Twilio\Rest\Content\V1\ContentCreateRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Utils\DateManager;
use App\Constants\TemplatesSidsConstants;

class TwilioService
{
    protected $twilioClient;

    public function __construct()
    {
        $this->twilioSid=env('TWILIO_SID');
        $this->twilioToken=env('TWILIO_AUTH_TOKEN');
        $this->twilioClient = new Client($this->twilioSid,$this->twilioToken);
        
    }

    /**
     * Envía un mensaje de WhatsApp con texto
     */
    public function sendWhatsAppMessage($to, $message)
    {   
        if (!Str::startsWith($to, "whatsapp:+")) {
            $to = "whatsapp:+57" . ltrim($to, "+");
        }
        try {
            $messageDetails = $this->twilioClient->messages->create(
                $to,
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                    'body' => $message,
                ]
            );
            return [
                'cost' => $messageDetails->price ?? 'N/A',
                'to' => $messageDetails->to,
            ];
        } catch (\Exception $e) {
            throw new ServerErrorException("Error al enviar mensaje de WhatsApp: " . $e->getMessage(), 500);
        }
    }
    public function sendInteractiveMessageLoginUser($to, array $users)
    {
        try {
            $options = $this->getClientsList($users);
            $templateSid = $this->createListPickerTemplate($options,$to);
            $this->createInterativeMessage($to,$templateSid,["1"=>"Diana"]);
            $this->deleteTemplateWhatsapp($templateSid); 
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar mensaje interactivo: " . $e->getMessage());
        }
    }


    public function sendInterativeMenuMessage($to,$client){
            $vars=["1"=>$client];
            $templateSid =TemplatesSidsConstants::MENU2TEMPLATE;
            $message = $this->createInterativeMessage($to,$templateSid,$vars);

    }
    public function sendInterativeMessageToCheckCitas($to, $data)
    {
        $templateSid =TemplatesSidsConstants::CONSULTCITASTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function sendInterativeMessageWithPdfCitas($to,$urlSubfije){
        
        $vars=["1"=>$urlSubfije];
        $templateSid =TemplatesSidsConstants::URLCITASPDFTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid,$vars);
        
    }
    public function sendEndBranchChatMessageInterative($to,$data){
        $templateSid = TemplatesSidsConstants::ENDBRANCHCHATTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function sendInterativeMessageError($to,$data){
        $templateSid = TemplatesSidsConstants::ERRORTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function sendInterativeMessageCancelCita($to){
        $templateSid = TemplatesSidsConstants::CANCELCITASTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
       
    }
    public function sendInterativeListEps($to,$data){
        $templateSid = TemplatesSidsConstants::LISTEPSTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function sendInterativeMessageUploadOrder($to,$data){
        $templateSid = TemplatesSidsConstants::UPLOADORDERTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
   
    }
    public function sendRequestNextClinicalHistoryImageInterativeMessage($to,$data){
        $templateSid = TemplatesSidsConstants::UPLOADNEXTHISTORYCLINICALTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
   
    }
    public function sendHandleObservationsInterativeMessage($to,$data){
        $templateSid = TemplatesSidsConstants::HANDLEOBSERVATIONSTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function requestParticularUserHaveHistoryClinical($to,$data){
        $templateSid = TemplatesSidsConstants::QUESTIONUSERCANHISTORYCLINICAL;
        $message = $this->createInterativeMessage($to,$templateSid);
    }
    public function consultOrdersInterativeMessage($to,$data){
        $templateSid = TemplatesSidsConstants::CONSULTORDERSTEMPLATE;
        $message = $this->createInterativeMessage($to,$templateSid);
    }

    public function sendInterativeMessageListCitasCancelables($to,$data){
        $optionsCitasCancelables=$this->getOptionCitasCancelables($data);
        $templateSid=$this->createListPickerTemplateToCancelCitas($to,$optionsCitasCancelables);
        $message= $this->createInterativeMessage($to,$templateSid);
        $this->deleteTemplateWhatsapp($templateSid);
        return $message;
    }
    public function sendInterativeMessageWhithOrdersCase($to,$data){
        $casesListed=$this->getOrdersCaseList($data);
        $templateSid=$this->createListPickerFromOrdersCase($casesListed,$to);
        $message= $this->createInterativeMessage($to,$templateSid);
        $this->deleteTemplateWhatsapp($templateSid);
        return $message;
    }
    private function getOrdersCaseList($orders){
        $buttonOrders=[];
        foreach ($orders as $order) {
            $day = DateManager::getDayWeekToDate($order['fecha_creacion']);
            $date = DateManager::getDshrtDate($order['fecha_creacion']);
            $id=$order['id'];
    
            $buttonOrders[] = [
                'item' => "Orden con Codigo $id",
                'description' => "Fecha creacion $day $date",
                'id' => $id,
            ];
        }
        return $buttonOrders;
    }
    private function createListPickerFromOrdersCase($items,$to){
        $url = "https://content.twilio.com/v1/Content";

        $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
            ->post($url, [
                "friendly_name" => "orders_$to",
                "language" => "es",
                "types" => [
                    "twilio/list-picker" => [
                        "body" => "Por favor seleciona una de los casos de ordenes presentados a continuación",
                        "button" => "Ordenes",
                        "items" => $items
                    ]
                ]
            ]);

        if ($response->successful()) {
            return $response->json()['sid'];
        } else {
            throw new \Exception("Error en la creación de la plantilla: " . $response->body());
        }
    }


    private function getOptionCitasCancelables($citas) {
        $buttonCitas = [];
    
        foreach ($citas as $cita) {
            $day = DateManager::getDayWeekToDate($cita['start']);
            $date = DateManager::getDshrtDate($cita['start']);
    
            $buttonCitas[] = [
                'item' => "$day $date",
                'description' => $cita["procedimiento"],
                'id' => $cita['ids'],
            ];
        }
    
        return $buttonCitas;
    }
    
    
    private function deleteTemplateWhatsapp($idTemplate){
        $url = "https://content.twilio.com/v1/Content/$idTemplate";

        $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
            ->delete($url);
    }
    public function createListPickerTemplateToCancelCitas($to,$options)
    {
        $url = "https://content.twilio.com/v1/Content";

        $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
            ->post($url, [
                "friendly_name" => "citas_cancelables_$to",
                "language" => "es",
                "types" => [
                    "twilio/list-picker" => [
                        "body" => "Por favor seleciona una de las citas que deseas cancelar en la lista acontinuacion",
                        "button" => "Citas",
                        "items" => $options
                    ]
                ]
            ]);

        if ($response->successful()) {
            return $response->json()['sid']; // Devuelve el SID del contenido creado
        } else {
            throw new \Exception("Error en la creación de la plantilla: " . $response->body());
        }
    }
    private function createInterativeMessage(string $to, string $sid,?array $contentVariables = null)
    {
        try {
            
            $params = [
                "from" => env('TWILIO_WHATSAPP_FROM'),
                "contentSid" => $sid
            ];
    
            if (!empty($contentVariables)) {
                $params["contentVariables"] = json_encode($contentVariables);
            }
    
            $message = $this->twilioClient->messages->create("whatsapp:+57" . $to, $params);
            
            return $message->sid;
        } catch (\Exception $e) {
            \Log::error("Error enviando mensaje interactivo: " . $e->getMessage());
            return false;
        }
    }
    public function createListPickerTemplate($options,$to)
    {
        $url = "https://content.twilio.com/v1/Content";

        $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
            ->post($url, [
                "friendly_name" => "list_picker_$to",
                "language" => "es",
                "variables" => [
                    "1" => "name"
                ],
                "types" => [
                    "twilio/list-picker" => [
                        "body" => "¡Hola! 👋 Te Saluda {{1}}, la asistente virtual de *Asopormen*. Estoy aquí para ayudarte. Por favor, elige tu usuario de la siguiente lista:",
                        "button" => "Usuarios",
                        "items" => $options
                    ]
                ]
            ]);

        if ($response->successful()) {
            return $response->json()['sid']; 
        } else {
            throw new \Exception("Error en la creación de la plantilla: " . $response->body());
        }
    }
        /**
     * Genera la lista de opciones dinámicamente
     */
    private function getClientsList(array $users)
    {
        $usersByButton = [];

        // Limitar a máximo 9 usuarios
        $users = array_slice($users, 0, 9);

        foreach ($users as $user) {
            $usersByButton[] = [
                'item' => mb_strimwidth($user['nombre'], 0, 20, "..."),
                'id' => $user['nombre']
            ];
        }

        // Agregar opción "Otro"
        $usersByButton[] = [
            'item' => 'Otro',
            'id' => 'Otro'
        ];

        return $usersByButton;
    }
    
}