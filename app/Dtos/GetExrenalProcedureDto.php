<?php

namespace App\Dtos;

class GetExrenalProcedureDto{
    private ?string $epsCode ;
    private ?string $covenantCode;
    private ?string $tarife;
    private ?string $code;
    public function __construct(?string $epsCode=null, ?string $covenantCode=null, ?string $tarife=null,?string $code=null)
    {
        $this->epsCode = $epsCode;
        $this->covenantCode = $covenantCode;
        $this->tarife = $tarife;
        $this->code = $code;
    }
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['epsCode'] ?? null,
            $data['covenantCode'] ?? null,
            $data['tarife'] ?? null,
            $data['code'] ?? null
            
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
    public function getCode(): ?string
    {
        return $this->code;
    }
    public function isValid(): bool
    {
        return $this->tarife !== null || ($this->epsCode !== null && $this->covenantCode !== null);
    }        
}