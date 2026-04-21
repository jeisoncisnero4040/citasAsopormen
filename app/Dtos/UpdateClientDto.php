<?php

namespace App\Dtos;




class UpdateClientDto
{
    private string $code;
    private ?string $deadCause;
    private string $action;


    public function __construct(
        string $code,
        string $action,
        ?string $deadCause = null,

    ) {
        $this->code = $code;
        $this->action = $action;
        $this->deadCause = $deadCause;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            action: $data['action'] ?? '',
            deadCause: $data['deadCause'] ?? null
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDeadCause(): ?string
    {
        return $this->deadCause;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}