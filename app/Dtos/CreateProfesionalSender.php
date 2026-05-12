<?php


namespace App\Dtos;

class CreateProfesionalSender
{
    public function __construct(
        public string $code,
        public string $documentNumber,
        public string $name,
        public int $documentType ,
        public ?string $phone = '',
        public ?string $address = '',
        public ?string $city = '',
        public ?string $email = '',
        public ?string $specialty = '',
        public ?string $ips = '',
    ) {
        
    }
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'] ?? '',
            documentNumber: $data['numberDocument'] ?? '',
            name: $data['name'] ?? '',
            documentType: $data['typeDocument'] ?? 0,
            phone: $data['phone'] ?? '',
            address: $data['address'] ?? '',
            city: $data['city'] ?? '',
            email: $data['email'] ?? '',
            specialty: $data['specialty'] ?? '',
            ips: $data['ips'] ?? ''
        );
    }


    public function getCode(): string
    {
        return $this->code;
    }
    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDocumentType(): int
    {
        return $this->documentType;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function getCity(): ?string
    {
        return $this->city;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }
    public function getIps(): ?string
    {
        return $this->ips;
    }
}