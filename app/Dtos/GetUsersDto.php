<?php

namespace App\Dtos;

class GetUsersDto {
    private bool $isSearch;
    private ?string $paramSeacrh = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $cedula = null;
    private bool $onlyAppointmentsPersonal = false;

    public function __construct(bool $isSearch, ?string $paramSeacrh = null, ?string $name = null, ?string $email = null, ?string $cedula = null, bool $onlyAppointmentsPersonal = false)
    {
        $this->isSearch = $isSearch;
        $this->paramSeacrh = $paramSeacrh;
        $this->onlyAppointmentsPersonal = $onlyAppointmentsPersonal;
        $this->name = $name;
        $this->email = $email;
        $this->cedula = $cedula;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            isSearch:(bool) $data['isSearch'],
            paramSeacrh: ($data['paramSearch'] ?? null),
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            cedula: $data['cedula'] ?? null,
            onlyAppointmentsPersonal: (bool) ($data['onlyAppointmentsPersonal'] ?? false)
        );
    }
    public function getIsSearch(): bool
    {
        return $this->isSearch;
    }
    public function getParamSeacrh(): ?string
    {
        return $this->paramSeacrh;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCedula(): ?string
    {
        return $this->cedula;
    }

    public function getOnlyAppointmentsPersonal(): bool
    {
        return $this->onlyAppointmentsPersonal;
    }
    public function isParamSeacrhOnlyNumeric(): bool
    {
        return is_numeric($this->paramSeacrh);
    }
    public function isParamSeacrhOnlyAlpha(): bool
    {
        return ctype_alpha(str_replace(' ', '', $this->paramSeacrh));
    }
}