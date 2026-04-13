<?php

namespace App\Dtos;



class CreateClientDto
{
    private string $documentType;
    private string $documentNumber;
    private string $regime;
    private string $entityCode;
    private string $covenantCode;

    private string $firstName;
    private ?string $middleName;
    private string $lastName;
    private ?string $secondLastName;

    private int $country;
    private ?string $maritalStatus;
    private ?string $bloodType;
    private ?int $childrenCount;
    private string $educationLevel;
    private string $zone;
    private ?string $birthPlace;

    private string $gender;
    private string $userType;
    private string $occupation;
    private string $municipality;
    private string $birthDate;

    private string $address;
    private string $neighborhood;
    private string $phone;
    private string $email;

    private bool $signConsent;

    private ?string $populationGroup;
    private ?string $ethnicity;
    private bool $hasDisability;
    private ?string $disabilityType;

    private string $guardianDocument;
    private string $guardianFirstName;
    private ?string $guardianMiddleName;
    private string $guardianLastName;
    private ?string $guardianSecondLastName;
    private string $guardianPhone;
    private string $guardianRelationship;

    private bool $active;

    private ?string $SisbenGroup=null;
    private ?string $observations=null;


    public function __construct(
        string $documentType,
        string $documentNumber,
        string $regime,
        string $entityCode,
        string  $covenantCode,
        string $firstName,
        ?string $middleName,
        string $lastName,
        ?string $secondLastName,
        int $country,
        ?string $maritalStatus,
        ?string $bloodType,
        ?int $childrenCount,
        string $educationLevel,
        string $zone,
        ?string $birthPlace,
        string $gender,
        string $userType,
        string $occupation,
        string $municipality,
        string $birthDate,
        string $address,
        string $neighborhood,
        string $phone,
        string $email,
        bool $signConsent,
        ?string $populationGroup,
        ?string $ethnicity,
        bool $hasDisability,
        ?string $disabilityType,
        string $guardianDocument,
        string $guardianFirstName,
        ?string $guardianMiddleName,
        string $guardianLastName,
        ?string $guardianSecondLastName,
        string $guardianPhone,
        string $guardianRelationship,

        bool $active,
        ?string $observations,
        ?string $SisbenGroup,



    ) {

        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->regime = $regime;
        $this->entityCode = $entityCode;
        $this->covenantCode = $covenantCode;

        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->secondLastName = $secondLastName;

        $this->country = $country;
        $this->maritalStatus = $maritalStatus;
        $this->bloodType = $bloodType;
        $this->childrenCount = $childrenCount;
        $this->educationLevel = $educationLevel;
        $this->zone = $zone;
        $this->birthPlace = $birthPlace;

        $this->gender = $gender;
        $this->userType = $userType;
        $this->occupation = $occupation;
        $this->municipality = $municipality;
        $this->birthDate = $birthDate;

        $this->address = $address;
        $this->neighborhood = $neighborhood;
        $this->phone = $phone;
        $this->email = $email;

        $this->signConsent = $signConsent;

        $this->populationGroup = $populationGroup;
        $this->ethnicity = $ethnicity;
        $this->hasDisability = $hasDisability;
        $this->disabilityType = $disabilityType;

        $this->guardianDocument = $guardianDocument;
        $this->guardianFirstName = $guardianFirstName;
        $this->guardianMiddleName = $guardianMiddleName;
        $this->guardianLastName = $guardianLastName;
        $this->guardianSecondLastName = $guardianSecondLastName;
        $this->guardianPhone = $guardianPhone;
        $this->guardianRelationship = $guardianRelationship;


        $this->active =$active;
        $this->SisbenGroup=$SisbenGroup;
        $this->observations=$observations;

        
    }

    public static function fromArray(array $data): self
    {
        return new self(

            documentType: $data['documento'],
            documentNumber: $data['numDoc'],
            regime: $data['regimen'],

            entityCode: $data['entidad'],
            covenantCode: $data['convenio'],

            firstName: $data['primer_nombre'],
            middleName: $data['segundo_nombre'] ?? '',
            lastName: $data['primer_apellido'],
            secondLastName: $data['segundo_apellido'] ?? '',

            country: (int)$data['pais'],
            maritalStatus: $data['estado_civil'] ?? '',
            bloodType: $data['rh'] ?? '',
            childrenCount: isset($data['num_hijos']) ? (int)$data['num_hijos'] : '',
            educationLevel: $data['escolaridad'],
            zone: $data['zona'],
            birthPlace: $data['lugar_nac'] ?? '',

            gender: $data['sexo'],
            userType: $data['tipo_usuario'],
            occupation: $data['ocupacion'],
            municipality: $data['municipio'],
            birthDate: $data['fecha_nac'],

            address: $data['direccion'],
            neighborhood: $data['barrio'],
            phone: $data['contacto'],
            email: $data['email'],

            signConsent: (bool)($data['firmar'] ?? false),

            populationGroup: $data['poblacion'] ?? '',
            ethnicity: $data['etnia'] ?? '',
            hasDisability: (bool)($data['discapacidad'] ?? false),
            disabilityType: $data['discapacidad_tipo'] ?? '',

            guardianDocument: $data['a_documento'],
            guardianFirstName: $data['a_primer_nombre'],
            guardianMiddleName: $data['a_segundo_nombre'] ?? '',
            guardianLastName: $data['a_primer_apellido'],
            guardianSecondLastName: $data['a_segundo_apellido'] ?? '',
            guardianPhone: $data['a_contacto'],
            guardianRelationship: $data['a_parentezco'],


            active:(bool) $data['activo']??false,
            SisbenGroup:$data['grupo']??'',
            observations:$data['observaciones']??'',

        );
    }
    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function getRegime(): string
    {
        return $this->regime;
    }

    public function getEntity(): string
    {
        return $this->entityCode;
    }

    public function getCovenant(): string
    {
        return $this->covenantCode;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getSecondLastName(): ?string
    {
        return $this->secondLastName;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }

    public function getBloodType(): ?string
    {
        return $this->bloodType;
    }

    public function getChildrenCount(): ?int
    {
        return $this->childrenCount;
    }

    public function getEducationLevel(): string
    {
        return $this->educationLevel;
    }

    public function getZone(): string
    {
        return $this->zone;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getOccupation(): string
    {
        return $this->occupation;
    }

    public function getMunicipality(): string
    {
        return $this->municipality;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSignConsent(): bool
    {
        return $this->signConsent;
    }

    public function getPopulationGroup(): ?string
    {
        return $this->populationGroup;
    }

    public function getEthnicity(): ?string
    {
        return $this->ethnicity;
    }

    public function hasDisability(): bool
    {
        return $this->hasDisability;
    }

    public function getDisabilityType(): ?string
    {
        return $this->disabilityType;
    }

    public function getGuardianDocument(): string
    {
        return $this->guardianDocument;
    }

    public function getGuardianFirstName(): string
    {
        return $this->guardianFirstName;
    }

    public function getGuardianMiddleName(): ?string
    {
        return $this->guardianMiddleName;
    }

    public function getGuardianLastName(): string
    {
        return $this->guardianLastName;
    }

    public function getGuardianSecondLastName(): ?string
    {
        return $this->guardianSecondLastName;
    }

    public function getGuardianPhone(): string
    {
        return $this->guardianPhone;
    }

    public function getGuardianRelationship(): string
    {
        return $this->guardianRelationship;
    }


    public function getStatus():bool{
        return $this->active;
    }
    public function getGroupSisben():?string{
        return $this->SisbenGroup;
    }
    public function getObsertvations():?string{
        return $this->observations;
    }

}