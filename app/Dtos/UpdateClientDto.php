<?php

namespace App\Dtos;

class UpdateClientDto
{
    private string $userRequest;
    private string $cedulaRequest;
    private string $code;
    private ?string $deadCause;
    private string $action;

    public function __construct(
        string $userRequest,
        string $cedulaRequest,
        string $code,
        string $action,
        ?string $deadCause = null
    ) {
        $this->userRequest = $userRequest;
        $this->cedulaRequest = $cedulaRequest;
        $this->code = $code;
        $this->action = $action;
        $this->deadCause = $deadCause;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            userRequest: $data['user'] ?? '',
            cedulaRequest: $data['cedula_user'] ?? '',
            code: $data['code'] ?? '',
            action: $data['action'] ?? '',
            deadCause: $data['deadCause'] ?? null
        );
    }

    public function getUserRequest(): string
    {
        return $this->userRequest;
    }

    public function getCedulaRequest(): string
    {
        return $this->cedulaRequest;
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