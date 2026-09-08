<?php

namespace App\Dtos;

use InvalidArgumentException;

class GetAgreementsDto
{
    public function __construct(
        private ?int $id=null,
        private ?string $epsCode=null,
        private ?string $agreementCode=null,
        private ?string $tarife = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            epsCode: $data['epsCode'] ?? null,
            agreementCode: $data['covenantCode'] ?? null,
            tarife:$data['tarife']
        );
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEpsCode(): ?string
    {
        return $this->epsCode;
    }

    public function getAgreementCode(): ?string
    {
        return $this->agreementCode;
    }
    public function getTarife():?String{
        return $this->tarife;
    }
}