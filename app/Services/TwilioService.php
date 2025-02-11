<?php

namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\utils\DateManager;
use Twilio\Rest\Client;
use Carbon\Carbon;

class TwilioService
{
    protected $twilioClient;

    public function __construct()
    {
        $this->twilioClient = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendWhatsAppMessage($to, $message)
    {
        try {
            $messageDetails = $this->twilioClient->messages->create(
                "$to",
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                    'body' => $message,
                ]
            );
            return [
                'cost' => $messageDetails->price,
                'to' => $messageDetails->to,
            ];
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }

    public function sendWhatsAppTemplate($request,$id)
    {   
        
        $templateSid = 'HXd4de99ea3f2f9712fbcbfb5e74b71c43'; 

        try {
            // Enviar el mensaje utilizando la plantilla
            $message = $this->twilioClient->messages->create(
                "whatsapp:+57{$request['telephone_number']}", 
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'), 
                    'contentSid' => $templateSid, 
                    'contentVariables' => json_encode(
                        [
                            '1' => $request['client'],
                            '2' => $this->formatDate($request['date']),
                            '3' => $request['direction'],  
                            '4' => $request['profesional'],
                            '5' => $request['procedim'],
                            '6'=>(string)$id,
                            '7'=>(string)$id,

                        ]
                    )
                ]
            );

            return [
                'success' => true,
                'messageSid' => $message->price
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    public function sendErrorSystemTemplate($request){
        $date = $request['date'];
        $origin = $request['origin'];
        $templateSid = 'HXbc367c960fb8b47ca2290a5cd3814dac'; 

        try {
            $message = $this->twilioClient->messages->create(
                "whatsapp:+57{$request['telephone_number']}", 
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'), 
                    'contentSid' => $templateSid, 
                    'contentVariables' => json_encode(
                        [
                            '1' => $date,
                            '2' => $origin,
                        ]
                    )
                ]
            );

            return [
                'success' => true,
                'messageSid' => $message->price];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    public function sendForgotPasswordTemplate($request){
        $nameClient=$request['clientName'];
        $password=$request['password'];
        $date=Carbon::now();
        $templateSid = 'HX937eefb4d882edc39b7ac97c6c4d72e2'; 

        try {
            $message = $this->twilioClient->messages->create(
                "whatsapp:+57{$request['telephoneNumber']}", 
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'), 
                    'contentSid' => $templateSid, 
                    'contentVariables' => json_encode(
                        [
                            '1' => $nameClient,
                            '2' => $password,
                            '3'=>$this->formatDate($date)
                        ]
                    )
                ]
            );

            return [
                'success' => true,
                'messageSid' => $message
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    public function sendConfirmationMessage($request)
    {   
        
        $templateSid = 'HX0d17559d2fec7067a57e404bf8c86303'; 

        try {
            // Enviar el mensaje utilizando la plantilla
            $message = $this->twilioClient->messages->create(
                "whatsapp:+57{$request['telephone_number']}", 
                [
                    'from' => env('TWILIO_WHATSAPP_FROM'), 
                    'contentSid' => $templateSid, 
                    'contentVariables' => json_encode(
                        [
                            '1' => $request['client'],
                            '2' => $request['authorization'],
                            '3' => $request['week_days'],  
                            '4' => $request['first_day'],
                            '5' => $request['laterDay'],


                        ]
                    )
                ]
            );

            return [
                'success' => true,
                'messageSid' => $message->price
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    private function formatDate($dateInString)
    {
        $date = Carbon::parse($dateInString);
        return DateManager::dateToStringFormat($date);
    }
    public function getMessagesWithNumber($number, $limit = 1000)
    {
        $numberToFilter = "whatsapp:+57$number";
    
        try {
            $messagesTo = $this->twilioClient->messages->read(
                [
                    'to' => $numberToFilter,
                ],
                $limit
            );
    
            $messagesFrom = $this->twilioClient->messages->read(
                [
                    'from' => $numberToFilter,
                ],
                $limit
            );
    
            $allMessages = array_merge($messagesTo, $messagesFrom);
            $history = [];
            foreach ($allMessages as $message) {
                $history[] = [
                    'sid' => $message->sid,
                    'from' => $message->from,
                    'to' => $message->to,
                    'body' => $message->body,
                    'status' => $message->status,
                    'date_sent' => $message->dateSent?->format('Y-m-d H:i:s'),
                ];
            }
            usort($history, function ($a, $b) {
                return strtotime($a['date_sent']) <=> strtotime($b['date_sent']);
            });
    
            return $history;
        } catch (\Exception $e) {
            throw new ServerErrorException($e->getMessage(), 500);
        }
    }
    
    

}

