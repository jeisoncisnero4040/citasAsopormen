<?php
namespace App\Domain;

use App\Domain\Code;

class Eps
{
    public function __construct(
        private readonly Code $code,
        private readonly Code $nit,
        private readonly string $name
    ){}

    public function getCode(): Code
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNit(): Code
    {
        return $this->nit;
    }
}