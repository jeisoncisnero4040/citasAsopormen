<?php

namespace App\Builders;

use App\Commands\AuthCommand;
use App\Models\Tarife;
use App\Dtos\CreateAuthDto;
use App\Dtos\ExternalProcedureDto;
use App\Models\ClientView;
use App\Models\UserRequesting;


class AuthBuilder{
    private ?CreateAuthDto $createAuthDto=null;
    private ?ClientView $clientView=null;
    private ?UserRequesting $userRequesting=null;
    private ?ExternalProcedureDto $externalProcedureDto=null;
    private ?Tarife $tarife=null;
    private string $now;
    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }
    public static function create(): self
    {
        return new self();
    }
    public function withCreateAuthDto(CreateAuthDto $createAuthDto): self
    {
        $this->createAuthDto = $createAuthDto;
        return $this;
    }
    public function withClientView(ClientView $clientView): self
    {
        $this->clientView = $clientView;
        return $this;   
    }
    public function withUserRequesting(UserRequesting $userRequesting): self
    {
        $this->userRequesting = $userRequesting;
        return $this;
    }
    public function withTarife(Tarife $tarife): self
    {
        $this->tarife = $tarife;
        return $this;   
    }
    public function withExternalProcedureDto(ExternalProcedureDto $externalProcedureDto): self
    {
        $this->externalProcedureDto = $externalProcedureDto;
        return $this;   
    }
    public function build(): AuthCommand
    {
        $this->validate();

        return new AuthCommand(
            cupCode: $this->externalProcedureDto->getCode(),
            amount: $this->externalProcedureDto->getQuantity(),
            date: $this->now,
            expiredDate: $this->createAuthDto->getTo(),
            epsCode: $this->clientView->getEpsCode(),
            clientCode: $this->clientView->getCode(),
            authCode: $this->createAuthDto->getAuthCode(),
            amountDays: $this->createAuthDto->getNumberDays(),
            userCreating: $this->userRequesting->getUsername(),
            dateCreating: $this->now,
            observations: $this->createAuthDto->getObservations(),
            startDate: $this->createAuthDto->getFrom(),
            covenantCode: $this->clientView->getCovenantCode(),
            remitente: $this->createAuthDto->getRemitente(),
            tarifeCode: $this->tarife->getCode()
        );
    }

    /**
     * @return AuthCommand[]
     */
    public function buildMany(array $procedures): array
    {
        $commands = [];

        foreach ($procedures as $procedure) {
            $commands[] = (new self())
                ->withCreateAuthDto($this->createAuthDto)
                ->withClientView($this->clientView)
                ->withUserRequesting($this->userRequesting)
                ->withTarife($this->tarife)
                ->withExternalProcedureDto($procedure)
                ->build();
        }

        return $commands;
    }
    private function validate(): void
    {
        if (!$this->createAuthDto) throw new \Exception('CreateAuthDto requerido');
        if (!$this->clientView) throw new \Exception('ClientView requerido');
        if (!$this->userRequesting) throw new \Exception('UserRequesting requerido');
        if (!$this->externalProcedureDto) throw new \Exception('ExternalProcedureDto requerido');
        if (!$this->tarife) throw new \Exception('Tarife requerido');
    }

}