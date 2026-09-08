<?php

namespace App\Models;

class AgreementsViewModel{
    public function __construct(
        private string $code,
        private string $epsCode,
        private string $name,
        private bool $isPbs
    ) {}

    public function getCode(): string
    {
        return $this->code;
    }

    public function getEpsCode(): string
    {
        return $this->epsCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isPbs(): bool
    {
        return $this->isPbs;
    }
}