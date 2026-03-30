<?php

namespace App\Dtos;

class GetExrenalProcedureDto{
    private ?string $epsCode;
    private ?string $covenantCode;
    private ?string $tarife;
    public function __construct(?string $epsCode, ?string $covenantCode, ?string $tarife)
    {
        $this->epsCode = $epsCode;
        $this->covenantCode = $covenantCode;
        $this->tarife = $tarife;
    }
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['epsCode'] ?? null,
            $data['covenantCode'] ?? null,
            $data['tarife'] ?? null
        );
    }
    public function getEpsCode(): ?string
    {
        return $this->epsCode;          
    }   
    public function getCovenantCode(): ?string
    {
        return $this->covenantCode;          
    }
    public function getTarife(): ?string
    {        return $this->tarife;          
    }
    public function isValid(): bool
    {
        return $this->tarife !== null || ($this->epsCode !== null && $this->covenantCode !== null);
    }        
}