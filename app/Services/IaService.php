<?php

namespace App\Services;

use App\Domain\IaProvider;
use App\Ports\IaPort;
use App\Domain\Prompt;
use App\Domain\IaResponse;

class IaService implements IaPort
{
    private IaProvider $iaProvider;

    public function __construct(IaProvider $iaProvider)
    {
        $this->iaProvider = $iaProvider;
    }


    public function executePrompt(Prompt $prompt): IaResponse
    {
        $response = $this->iaProvider->client()->chat()->create([
            'model' => $prompt->model() ?? 'gpt-4o',
            'messages' => [
                [
                    'role' => $prompt->role() ?? 'user',
                    'content' => $prompt->input() ?? ''
                ]
            ],
        ]);

        $content = $response->choices[0]->message->content ?? '';

        return IaResponse::fromString($content);
    }   
        public function executeTest(): array
    {
        $response = $this->iaProvider->client()->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'cual es la capital de francia?'
                ]
            ],
        ]);

        return [
            'response' => $response->choices[0]->message->content
        ];
    }
}