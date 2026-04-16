<?php

namespace App\Models;


class Audit 
{
    private int $id;
    private string $module;
    private string $description;
    private string $userId;
    private string $createdAt;
    private string $userName;

    public function __construct(int $id, string $module, string $description, string $userId, string $createdAt, string $userName)
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
            userId: $data['cedula_usuario'],
            createdAt: $data['fecha_creacion'],
            userName: $data['nombre']
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