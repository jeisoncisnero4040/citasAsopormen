<?php


namespace App\Dtos;

class DeleteAuthsDto {
    private ?string $authCode;
    private ?string $clientCode;
    private ?string $nro;
    private ?int $id;

    public function __construct(?string $authCode, ?string $clientCode, ?string $nro = null, ?int $id = null)
    {
        $this->authCode = $authCode;
        $this->clientCode = $clientCode;
        $this->nro = $nro;
        $this->id = $id;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            authCode: $data['authCode'] ?? null,
            clientCode: $data['clientCode'] ?? null,
            nro: $data['nro'] ?? null,
            id: $data['id'] ?? null
        );
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function getClientCode(): ?string
    {
        return $this->clientCode;
    }

    public function getNro(): ?string
    {
        return $this->nro;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}