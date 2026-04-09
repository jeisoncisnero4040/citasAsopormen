<?php

namespace App\Models;


class Tarife{
    private int $id;
    private string $code;
    private string $epsCode;
    private string $covenantCode;   

    public function __construct(int $id, string $code, string $epsCode, string $covenantCode)
    {
        $this->id = $id;
        $this->code = $code;
        $this->epsCode = $epsCode;
        $this->covenantCode = $covenantCode;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            code: $data['tarifa'] ?? '',
            epsCode: $data['eps'] ?? '',
            covenantCode: $data['convenio'] ?? ''
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getEpsCode(): string
    {
        return $this->epsCode;
    }

    public function getCovenantCode(): string
    {
        return $this->covenantCode;
    }
    public function getId(): int
    {
        return $this->id;
    }
}