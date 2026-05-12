<?php

namespace App\Commands;
use App\Models\UserRequesting;
use App\Dtos\CreateProfesionalSender;

class ProfesionalSenderCommand
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
        
        public ?\DateTime $registrationDate = new \DateTime(),
        public ?string $registeredBy = '',
        public ?string $specialty = '',
        public ?string $ips = '',
        public ?int $id= null
    ) {
        
    }

    public static function create(string $code,string $documentNumber,string $name,int $documentType): self {
        return new self(code: $code,documentNumber: $documentNumber,name: $name,documentType: $documentType);

    }
    public static function fromDto(CreateProfesionalSender $dto){
        return new self(
            code: $dto->getCode(),
            documentNumber: $dto->getDocumentNumber(),
            name: $dto->getName(),
            documentType: $dto->getDocumentType(),
            phone: $dto->getPhone(),
            address: $dto->getAddress(),
            city: $dto->getCity(),
            email: $dto->getEmail(),
            specialty: $dto->getSpecialty(),
            ips: $dto->getIps()
        );
    }
    public function setUserRequesting(UserRequesting $user): void
    {
        $this->registeredBy = $user->getCedula();
    }
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }
    public function setEmail(?string $email): void{
        $this->email = $email;
    }
    public function setSpecialty(?string $specialty): void{
        $this->specialty = $specialty;
    }
    public function setIps(?string $ips): void{
        $this->ips = $ips;
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
    public function getRegistrationDate(): ?string
    {
        return $this->registrationDate->format('Y-m-d H:i:s');
    }
    public function getRegisteredBy(): ?string
    {
        return $this->registeredBy;
    }
    public function logCreate(): string
    {
        return "El usuario {$this->registeredBy} creo el profesional remitente con codigo {$this->code} el dia {$this->getRegistrationDate()}";
    }




    
}