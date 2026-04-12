<?php

namespace App\Domain;

use App\Config\Prompt;
use App\Contracts\Inputable;
use Symfony\Component\Console\Input\Input;

class InputPrompt implements Inputable
{
    public function __construct(
        private readonly string $stream,
        private readonly string $context
    ) {}

    public function getInput(): string
    {
        return 
            Prompt::AGENT . "\n\n" .
            $this->stream . "\n\n" .
            "Contexto de la conversación:\n" .
            $this->context;
    }

    public function context(): string
    {
        return $this->context;
    }
}