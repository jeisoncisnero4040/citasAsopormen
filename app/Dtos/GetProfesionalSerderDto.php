<?php

namespace App\Dtos;

class GetProfesionalSerderDto
{
    private ?string $name;
    private ?string $code;
    private ?string $coincidenceParam;

    public function __construct(?string $name, ?string $code, ?string $coincidenceParam)
    {
        $this->name = $name;
        $this->code = $code;
        $this->coincidenceParam = $coincidenceParam;    
    }
    public static function fromArray(array $data): self
    {   
        return new self(
            name: $data['nombre'] ?? null,
            code: $data['cod'] ?? null,
            coincidenceParam: $data['param'] ?? null
        );
    }
    public function getCoincidenceParam(): ?string
    {
        return $this->coincidenceParam;
    }
    public function isParamACode(): bool
    {
        $normalizedParam = str_replace([' ', '-'], '', $this->coincidenceParam);
        return is_string($normalizedParam)
            && preg_match('/^[0-9]+$/', $normalizedParam) === 1;
    }

    public function isParamAName(): bool
    {
        return is_string($this->coincidenceParam)
            && preg_match('/^[a-zA-Z\s]+$/', $this->coincidenceParam) === 1;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }   
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}