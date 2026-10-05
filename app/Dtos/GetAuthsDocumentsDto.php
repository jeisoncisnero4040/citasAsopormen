<?php

namespace App\Dtos;

use InvalidArgumentException;

class GetAuthsDocumentsDto
{
    private ?int $id;
    private ?string $n_autoriza;
    private ?string $clientCode;
    private ?int $typeDocumentId;

    public function __construct(
        ?int $id = null,
        ?string $n_autoriza = null,
        ?string $clientCode = null,
        ?int $typeDocumentId = null
    ) {
        $this->id = $id;
        $this->n_autoriza = $n_autoriza;
        $this->clientCode = $clientCode;
        $this->typeDocumentId = $typeDocumentId;
    }

    public static function fromArray(array $data): self
    {

        return new self(
            $data['id'] ?? null,
            $data['n_autoriza'] ?? null,
            $data['clientCode'] ?? null,
            $data['typeDocumentId'] ?? null
        );
    }
    public static function fromId(int $id): self
    {
        return new self(id: $id);
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNAutoriza(): ?string
    {
        return $this->n_autoriza;
    }
    public function getClientCode(): ?string
    {
        return $this->clientCode;       
    }
    public function getTypeDocumentId(): ?int
    {
        return $this->typeDocumentId;
    }
    public function isEmptyRequiredFields(): bool
    {
        return empty($this->n_autoriza) && empty($this->clientCode) && empty($this->id) ;
    }
    public function isById(): bool
    {
        return !empty($this->id);
    }
    public function isByNAutorizaAndClientCode(): bool
    {
        return !empty($this->n_autoriza) && !empty($this->clientCode);
    }
    public function isValid(): void
    {
        if($this->isEmptyRequiredFields()) {
            throw new InvalidArgumentException('Todos los campos obligatorios están vacíos.');
        }
        if(!$this->isById() && !$this->isByNAutorizaAndClientCode()) {
            throw new InvalidArgumentException('El numero de autorización y el código del cliente son requeridos si no se proporciona un ID.');
        }
    }
}