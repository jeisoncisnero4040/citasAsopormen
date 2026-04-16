<?php

namespace App\Dtos;

class GetAuditDto
{
    private ?string $user=null;
    private ?string $client=null;
    private ?string $profesional=null;
    private ?int $idAppoinment=null;
    private ?string $authCode=null;
    private ?string $from=null;
    private ?string $to=null;

    public function __construct(
        ?string $user,
        ?string $client,
        ?string $profesional,
        ?int $idAppoinment,
        ?string $authCode,
        ?string $from,
        ?string $to
    ) {
        $this->user = $user;
        $this->client = $client;
        $this->profesional = $profesional;
        $this->idAppoinment = $idAppoinment;
        $this->authCode = $authCode;
        $this->from = $from;
        $this->to = $to;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user'] ?? null,
            $data['client'] ?? null,
            $data['profesional'] ?? null,
            isset($data['idAppoinment']) ? (int)$data['idAppoinment'] : null,
            $data['authCode'] ?? null,
            $data['from'] ?? date('Y-m-d 00:00:00'),
            $data['to'] ?? date('Y-m-d 23:59:59')
        );
    }

    public function getUser(): ?string
    {
        return $this->user;
    }
    public function getClient(): ?string
    {
        return $this->client;
    }
    public function getProfesional(): ?string
    {
        return $this->profesional;
    }
    public function getIdAppoinment(): ?int
    {
        return $this->idAppoinment;
    }
    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }
    public function getFrom(): string
    {
        return $this->from;
    }
    public function getTo(): string
    {
        $date = new \DateTimeImmutable($this->to);
        return $date
            ->setTime(23, 59, 59)
            ->format('Y-m-d H:i:s');
    }
    public function hasUser(): bool
    {
        return !empty($this->user);
    }
    public function hasClient(): bool
    {
        return !empty($this->client);
    }
    public function hasProfesional(): bool
    {
        return !empty($this->profesional);
    }
    public function hasIdAppoinment(): bool
    {
        return $this->idAppoinment !== null;
    }
    public function hasAuthCode(): bool
    {
        return !empty($this->authCode);
    }
    public function hasRangeTime(): bool
    {
        return !empty($this->from) && !empty($this->to);
    }
}