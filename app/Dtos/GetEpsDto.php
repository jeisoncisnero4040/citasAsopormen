<?php
namespace App\Dtos;
use App\Domain\Code;

class GetEpsDto
{
    public function __construct(
        private readonly ?Code $code = null,
        private readonly ?Code $nit = null
    ){}

    public function getCode(): ?Code
    {
        return $this->code;
    }

    public function getNit(): ?Code
    {
        return $this->nit;
    }
}