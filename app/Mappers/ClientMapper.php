<?php

namespace App\Mappers;

use App\Commands\ClientCommand;
use App\Dtos\CreateClientDto;
use App\Models\District;
use Illuminate\Support\Carbon;

class ClientMapper
{
    public static function clientDtoToClient(CreateClientDto $dto,District $municipality,bool $isNew=true): ClientCommand
    {
        return ClientCommand::create(isNew:$isNew)
            ->setTypeDoc($dto->getDocumentType())
            ->setNumDoc($dto->getDocumentNumber())
            ->setRegimen($dto->getRegime())
            ->setEps($dto->getEntity())
            ->setCovenat($dto->getCovenant())

            ->setFirstName($dto->getFirstName())
            ->setMiddleName($dto->getMiddleName())
            ->setLastName($dto->getLastName())
            ->setSecondLastName($dto->getSecondLastName())

            ->setMaritalStatus($dto->getMaritalStatus())
            ->setRh($dto->getBloodType())
            ->setNumHijos($dto->getChildrenCount())
            ->setAcademicLevel($dto->getEducationLevel())

            ->setZone($dto->getZone())
            ->setPlaceBirth($dto->getBirthPlace())
            ->setSex($dto->getGender())
            ->setUserType($dto->getUserType())
            ->setOcupation($dto->getOccupation())

            ->setDistric($dto->getMunicipality())
            ->setBirthDate($dto->getBirthDate())
            ->setDirection($dto->getAddress())
            ->setNeighborhood($dto->getNeighborhood())

            ->setPhone($dto->getPhone())
            ->setEmail($dto->getEmail())
            ->setCanSing($dto->getSignConsent())

            ->setPopulationGroup($dto->getPopulationGroup())
            ->setEtnia($dto->getEthnicity())

            ->setHasDissabled($dto->hasDisability())
            ->setTypeDissabled($dto->getDisabilityType())

            ->setGuardianDocument($dto->getGuardianDocument())
            ->setGuardianFirstName($dto->getGuardianFirstName())
            ->setGuardianMiddleName($dto->getGuardianMiddleName())
            ->setGuardianLastName($dto->getGuardianLastName())
            ->setGuardianSecondLastName($dto->getGuardianSecondLastName())
            ->setGuardianPhone($dto->getGuardianPhone())
            ->setGuardianRelationship($dto->getGuardianRelationship())
            ->setCountry($dto->getCountry())

            ->setUser($dto->getRequestUser())
            ->setDpto($municipality->getDpto())
            ->setDateRegister(Carbon::now()->format('Y-m-d H:i:s'))
            ->setIsActive($dto->getStatus())
            ->setSisben($dto->getGroupSisben())
            ->setObserva($dto->getObsertvations());
    }
}