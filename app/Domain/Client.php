<?php
namespace App\Domain;

class Client
{
    private string $id;
    private string $name;
    private int $age;
    private ?string $documentNumber;


    public function __construct(string $id, string $name, int $age, ?string $documentNumber = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->documentNumber = $documentNumber;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            age: (int)($data['age'] ?? 0),
            documentNumber: $data['document_number'] ?? null
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }
}