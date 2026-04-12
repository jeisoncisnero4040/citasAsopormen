<?php

namespace App\Domain;


use App\Domain\Prompt;
use App\Domain\Chat;
USE App\Config\Prompt as PromptConfig;

class PromptBuilder
{
    private string $model = 'gpt-5-nano';
    private string $agent = PromptConfig::AGENT;
    private string $stream = '';
    private string $context = '';
    private string $role = 'user';

    public static function fromChat(Chat $chat): self
    {
        $builder = new self();

        return $builder
            ->withContext($chat->toContextString());
    }

    public function withModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }
    public function withRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function withContext($context): self
    {
        $this->context = $context;
        return $this;
    }
    public function withStream(string $stream): self
    {
        $this->stream = $stream;
        return $this;
    }
    public function getInput(): string
    {
        return 
            $this->agent . "\n\n" .
            $this->stream . "\n\n" .
            "Contexto de la conversación:\n" .
            $this->context;
    }

    public function build(): Prompt
    {
        return new Prompt(
            model: $this->model,
            input: $this->getInput(),
            role:$this->role
        );
    }
}