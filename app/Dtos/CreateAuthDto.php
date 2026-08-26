<?php


namespace App\Dtos;

use App\Exceptions\CustomExceptions\BadRequestException;
use App\Dtos\ExternalProcedureDto;


final class CreateAuthDto
{
    private string $authCode;
    private string $from;
    private string $to;
    private int $numberDays;
    private string $observations;
    private string $remitente;
    private string $clientCode;
    private bool $isTemporal;

    /** @var ExternalProcedureDto[] */
    private array $procedures;

    public function __construct(
        string $authCode,
        string $from,
        string $to,
        int $numberDays,
        string $observations,
        string $remitente,
        array $procedures,
        string $clientCode,
        bool $isTemporal
    ) {
        if(empty($clientCode)) {
            throw new BadRequestException('El código del cliente es obligatorio',400);
        }
        if (empty($authCode) && !$isTemporal) {
            throw new BadRequestException('El número de autorización es obligatorio para autorizaciones no temporales',400);
        }

        if (empty($from) || empty($to)) {
            throw new BadRequestException('El rango de fechas es obligatorio',400);
        }

        if ($numberDays <= 0) {
            throw new BadRequestException('El número de sesiones debe ser mayor a 0',400);
        }
        

        $this->authCode = $authCode;
        $this->from = $from;
        $this->to = $to;
        $this->numberDays = $numberDays;
        $this->observations = trim($observations);
        $this->remitente = $remitente;
        $this->procedures = $procedures;
        $this->clientCode = $clientCode;
        $this->isTemporal = $isTemporal;

    }

    public static function fromArray(array $data): self
    {
        $procedures = array_map(
            fn ($proc) => ExternalProcedureDto::fromArray($proc),
            $data['procedures'] ?? []
        );

        return new self(
            authCode: $data['authCode'] ?? '',
            from: $data['from'] ?? '',
            to: $data['to'] ?? '',
            numberDays: (int) ($data['numberDays'] ?? 0),
            observations: $data['observations'] ?? '',
            remitente: $data['remitente'] ?? '',
            procedures: $procedures,
            clientCode: $data['clientCode'] ?? '',
            isTemporal: (bool) ($data['isTemporal'] ?? false)
        );
    }

    public function getAuthCode(): string { return $this->authCode; }
    public function getFrom(): string { return $this->from; }
    public function getTo(): string { return $this->to; }
    public function getNumberDays(): int { return $this->numberDays; }
    public function getObservations(): string { return $this->observations; }
    public function getRemitente(): string { return $this->remitente; }
    public function getClientCode(): string { return $this->clientCode; }
    public function getIsTemporal(): bool { return $this->isTemporal; }

    /** @return ExternalProcedureDto[] */
    public function getProcedures(): array { return $this->procedures; }
}