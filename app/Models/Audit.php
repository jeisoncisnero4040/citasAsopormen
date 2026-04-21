<?php

namespace App\Models;


class Audit 
{
    private int $id;
    private string $module;
    private string $description;
    private ?string $userId = null;
    private string $createdAt;
    private ?string $userName = null;

    public function __construct(int $id, string $module, string $description, ?string $userId = null, string $createdAt, ?string $userName = null)
    {
        $this->id = $id;
        $this->module = $module;
        $this->description = $description;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->userName = $userName;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            module: $data['modulo'],
            description: $data['descripcion'],
            userId: $data['cedula_usuario']??null,
            createdAt: $data['fecha_creacion'],
            userName: $data['nombre']??null
        );
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'modulo' => $this->module,
            'descripcion' => $this->description,
            'cedula_usuario' => $this->userId,
            'fecha_creacion' => $this->createdAt,
            'nombre' => $this->userName
        ];
    }
}