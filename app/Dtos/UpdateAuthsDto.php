<?php

namespace App\Dtos;
use App\Dtos\ExternalProcedureDto;

class UpdateAuthsDto
{
    private string $nro;
    private string $authCode;
    private string $from;
    private string $to;
    private int $numberDays;
    private string $clientCode;
    /** @var ExternalProcedureDto[] */
    private array $cups;
    private ?string $senderCode;
    private ?string $observations;

    public function __construct(
        string $nro,
        string $authCode,
        string $from,
        string $to,
        int $numberDays,
        string $clientCode,
        array $cups = [],
        ?string $senderCode = null,
        ?string $observations = ''
    ) {
        $this->nro = $nro;
        $this->authCode = $authCode;
        $this->from = $from;
        $this->to = $to;
        $this->numberDays = $numberDays;
        $this->clientCode = $clientCode;
        $this->cups = $cups;
        $this->senderCode = $senderCode;
        $this->observations = $observations;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nro: $data['nro'] ?? '',
            authCode: $data['n_autoriza'] ?? '',
            from: $data['f_inicial'] ?? '',
            to: $data['f_vence'] ?? '',
            numberDays: (int) ($data['days'] ?? 0),
            clientCode: $data['clientCod'] ?? '',
            cups: isset($data['cups']) ? array_map(fn($cup) => ExternalProcedureDto::fromArray($cup), $data['cups']) : [],
            senderCode: $data['cod_remitente'] ?? null,
            observations: $data['observations'] ?? null
        );
    }

    public function getNro(): string
    {
        return $this->nro;
    }

    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getNumberDays(): int
    {
        return $this->numberDays;
    }

    public function getClientCode(): string
    {
        return $this->clientCode;
    }

    /** @return ExternalProcedureDto[] */
    public function getCups(): array
    {
        return $this->cups;
    }

    public function getSenderCode(): ?string
    {
        return $this->senderCode;
    }
    public function getObservations(): ?string
    {
        return $this->observations;
    }
}