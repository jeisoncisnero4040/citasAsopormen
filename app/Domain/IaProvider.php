<?php

namespace App\Domain;

use OpenAI;
use OpenAI\Client;

class IaProvider
{
    private Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function client(): Client
    {
        return $this->client;
    }
}