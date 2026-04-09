<?php

namespace App\Dtos;
use App\Models\UserRequesting;
class DeleteAppoDto
{
    private int $id;
    private string $cliente;
    private ?UserRequesting $userRequest;

    public function __construct(
        int $id,
        string $cliente,
        ?UserRequesting $userRequest = null

    ) {
        $this->id = $id;
        $this->cliente = $cliente;
        $this->userRequest = $userRequest;
    }
    public static function fromRequest(array $query): self
    {
        return new self(

            cliente: $query['cliente'],
            id: (int)$query['id']
        );
    }
    public function setUserRequest(UserRequesting $userRequest): void
    {
        $this->userRequest = $userRequest;
    }
    public function getUserRequest(): ?UserRequesting
    {
        return $this->userRequest;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getCliente(): string
    {
        return $this->cliente;
    }


}
