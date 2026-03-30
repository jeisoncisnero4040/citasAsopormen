<?php

namespace App\Models;
use App\Interfaces\Serializable;


class ExternProcedure implements Serializable{
    private string $code;
    private string $description;
    private string $specialty;
    private bool $isActive;
    
    public function __construct(string $code, string $description, string $specialty, bool $isActive = true)
    {
        $this->code = $code;
        $this->description = $description;
        $this->specialty = $specialty;
        $this->isActive = $isActive;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['cod'],
            $data['nombre'],
            $data['especialidad'],
            (bool)($data['inactivo'] ?? true)
        );
    }
    public function getCode(): string
    {
        return $this->code;
    }   
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getSpecialty(): string
    {
        return $this->specialty;            
    }
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'nombre' => $this->description,
            'especialidad' => $this->specialty,
            'inactivo' => $this->isActive,

        ];
    }

}