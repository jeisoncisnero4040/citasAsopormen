<?php

namespace App\Dtos;

class DeleteAppoDto
{
    private int $id;
    private string $usuario;
    private string $cliente;
    private string $cedula;

    public function __construct(
        int $id,
        string $usuario,
        string $cliente,
        string $cedula
    ) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->cliente = $cliente;
        $this->cedula = $cedula;
    }

    public static function fromRequest(array $query): self
    {
        return new self(
            id: (int) $query['id'],
            usuario: $query['usuario'],
            cliente: $query['cliente'],
            cedula: $query['cedula']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsuario(): string
    {
        return $this->usuario;
    }

    public function getCliente(): string
    {
        return $this->cliente;
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }
}
