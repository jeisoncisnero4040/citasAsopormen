<?php

namespace App\Commands;

use App\Dtos\UpdateAuthsDto;
use App\Models\UserRequesting;
use App\Dtos\ExternalProcedureDto;
use App\Domain\Consecutive;
use App\Exceptions\CustomExceptions\ForbidenException;
use App\Domain\Date;
use App\Domain\Code;

final class AuthCommand
{


    public function __construct(
        private string $cupCode,
        private int $amount,
        private string $date,
        private string $expiredDate,
        private string $epsCode,
        private string $clientCode,
        private string $authCode,
        private int $amountDays,
        private string $userCreating,
        private string $dateCreating,
        private string $observations,
        private string $startDate,
        private string $covenantCode,
        private ?string $remitente,
        private ?string $tarifeCode,
        private bool $isTempory,

        private Consecutive $consecutive,
        private bool $anulated = false,
        private string $userAnulating = '',
        private int $assistedSessionsCounter = 0,
        private string $motiveAnulation = '',
        private bool $suspended = false,
        private string $headQuarters = '001',
        private bool $closed = false,
        private ?string $dateClosed = null,
        private ?string $userClosed = null,
        private ?string $motiveClosed = null,
        private ?string $userClosedDate = null,
        private ?string $changuesAsp = null,
        private bool $newSystem = true,
        private ?int $id = null,
        private ?Date $dateAdmision = null,
        private ?string $userAdmision = null,
        private ?Code $epsNit = null,
    ){
    }


    public function getCupCode(): string { return $this->cupCode; }
    public function getAmount(): int { return $this->amount; }
    public function getDate(): string { return $this->date; }
    public function getExpiredDate(): string { return $this->expiredDate; }
    public function getClientCode(): string { return $this->clientCode; }
    public function getAuthCode(): string { return $this->authCode; }
    public function getUserCreating(): string { return $this->userCreating; }
    public function getObservations(): string { return $this->observations; }
    public function getRemitente(): ?string { return $this->remitente; }
    public function getCovenantCode(): string { return $this->covenantCode; }
    public function getEpsCode(): string { return $this->epsCode; }
    public function getAmountDays(): int { return $this->amountDays; }
    public function isAnulated(): bool { return $this->anulated; }
    public function isSuspended(): bool { return $this->suspended; }
    public function isClosed(): bool { return $this->closed; }
    public function getHeadQuarters(): string { return $this->headQuarters; }
    public function getDateCreating(): string { return $this->dateCreating; }
    public function getUserAnulating(): string { return $this->userAnulating; }
    public function getMotiveAnulation(): string { return $this->motiveAnulation; }
    public function getConsecutive(): Consecutive { return $this->consecutive; }
    public function getDateClosed(): ?string { return $this->dateClosed; }
    public function getUserClosed(): ?string { return $this->userClosed; }
    public function getMotiveClosed(): ?string { return $this->motiveClosed; }
    public function getUserClosedDate(): ?string { return $this->userClosedDate; }
    public function getChanguesAsp(): ?string { return $this->changuesAsp; }
    public function getTarifeCode(): ?string { return $this->tarifeCode; }
    public function isNewSystem(): bool { return $this->newSystem; }  
    public function getDateAdmision(): ?Date { return $this->dateAdmision; }
    public function getUserAdmision(): ?string { return $this->userAdmision; }
    
    public function getStartDate(): string { return $this->startDate; }
    public function isTempory(): bool { return $this->isTempory; }
    public function getAssistedSessionsCounter(): int { return $this->assistedSessionsCounter; }

    public function setConsecutive(?Consecutive $consecutive): void { $this->consecutive = $consecutive; } 
    public function getId(): ?int { return $this->id; }
    public function getEpsNit(): ?Code { return $this->epsNit; }
    
    public function getMsmCreate(array $ids): string
    {
        return "El usuario {$this->userCreating} creo la autorización con codigo {$this->authCode}  el dia {$this->dateCreating}.
        con los siguientes ids de autorizaciones: " . implode(", ", $ids) .
        " el dia {$this->dateCreating}";
    }
    public function isUpdatableAmmount(): bool
    {
        return $this->amount > 0 && !$this->closed  && !$this->isExpired();
    }
    public function isExpired(): bool
    {
        $currentDate = new \DateTime();
        $currentDate->setTime(0, 0, 0);
        $expirationDate = new \DateTime($this->expiredDate);
        $expirationDate->setTime(0, 0, 0);
        return $currentDate > $expirationDate;
    }
    public function update(UpdateAuthsDto $dto,bool $codeIsChanged, UserRequesting $userRequesting): void
    {
        $this->authCode = $dto->getAuthCode();
        $this->startDate = $dto->getFrom();
        $this->expiredDate = $dto->getTo();
        $this->amountDays = $dto->getNumberDays();
        $this->remitente = $dto->getSenderCode() ;
        $this->amount = $this->resolveAmmountToUpdate($dto->getCups());
        $this->observations = trim($dto->getObservations() ?? '');
        $this->covenantCode = $dto->getCovenant();
        $this->epsCode = $dto->getCodEps();
        if($codeIsChanged && $this->isTempory) {
            if (!$userRequesting->isAdmisionUser()) {
                throw new ForbidenException("Solo los usuarios de nómina pueden cambiar el código de autorización temporal", 403);
            }
            $this->isTempory = false;
            $this->dateAdmision = Date::now();
            $this->userAdmision = $userRequesting->getUsername();
        }
        
    }
    public function updateWithProcedure(UpdateAuthsDto $dto,bool $codeIsChanged, UserRequesting $userRequesting, ExternalProcedureDto $procedureDto): void
    {

        $this->authCode = $dto->getAuthCode();
        $this->startDate = $dto->getFrom();
        $this->expiredDate = $dto->getTo();
        $this->amountDays = $dto->getNumberDays();
        $this->remitente = $dto->getSenderCode() ;
        $this->cupCode = $procedureDto->getCode();

        $this->amount = $this->resolveAmmountToUpdate([$procedureDto]);
        $this->observations = trim($dto->getObservations() ?? '');
        $this->covenantCode = $dto->getCovenant();
        $this->epsCode = $dto->getCodEps();
        if($codeIsChanged && $this->isTempory) {
            if (!$userRequesting->isAdmisionUser()) {
                throw new ForbidenException("No tienes permisos para cambiar el código de autorización temporal", 403);
            }
            $this->isTempory = false;
            $this->dateAdmision = Date::now();
            $this->userAdmision = $userRequesting->getUsername();
        }
    }

    /**
     * @param ExternalProcedureDto[] $cupsDto
     */
    private function resolveAmmountToUpdate(array $cupsDto): int
    {
        if(!$this->isUpdatableAmmount()) {
            return $this->amount;
        }
        $found = array_values(array_filter($cupsDto, fn($cup) => $cup->getCode() === $this->cupCode))[0] ?? null;
        return $found ? $found->getQuantity() : $this->amount;
    }
    public function deleteLog(array $idsApposAsossiate, UserRequesting $user): string
    {
       return "el usuario {$user->getUsername()} ha eliminado la autorización con numero de autorizacion {$this->authCode}
         y los siguientes las citas asociadas con ids : ". implode(", ", $idsApposAsossiate) . " el dia ". date("Y-m-d H:i:s");
    }

}