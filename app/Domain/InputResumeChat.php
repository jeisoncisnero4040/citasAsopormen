<?php

namespace App\Domain;
use App\Contracts\Inputable;
use App\Config\Prompt;

class InputResumeChat implements Inputable
{

    public function __construct(
        private readonly string $context
    ) {}

    public function getInput(): string
    {
        return 

            Prompt::RESUME . "\n\n" .
            "Contexto de la conversación:\n" .
            $this->context;
    }

    public function context(): string
    {
        return $this->context;
    }
}