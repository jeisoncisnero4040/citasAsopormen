<?php

namespace App\Domain;
use Twilio\Rest\Client;

class WhatsappSenderProvider{

    protected Client $client;

    public function __construct()
    {
        $this->client= new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }
    public function getClient(): Client
    {
        return $this->client;
    }
}