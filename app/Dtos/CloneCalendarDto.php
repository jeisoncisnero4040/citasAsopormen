<?php

namespace App\Dtos;
use App\Exceptions\CustomExceptions\BadRequestException as CustomExceptionsBadRequestException;
use App\Models\UserRequesting;

final class CloneCalendarDto
{
    private string $from;
    private string $to;
    private string $start;
    private string $cedula;
    private UserRequesting $userRequest;
    private string $profesional;

    public function __construct(
        string $from,
        string $to,
        string $start,
        string $cedula,
        UserRequesting $userRequest,
        string $profesional
    ) {
        if (empty($from) || empty($to)) {
            throw new CustomExceptionsBadRequestException('Las fechas from y to son obligatorias',400);
        }



        $this->from = $from;
        $this->to = $to;
        $this->start = $start;
        $this->cedula = $cedula;
        $this->userRequest = $userRequest;
        $this->profesional = $profesional;
    }

    public static function fromArray(array $data,array $dataUserRequest): self
    {
        return new self(
            from: $data['from'] ?? '',
            to: $data['to'] ?? '',
            userRequest: UserRequesting::fromArray($dataUserRequest),
            start: $data['start'] ?? '',
            cedula: $data['cedula'] ?? '',
            profesional: $data['profesional'] ?? '',
        );
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getUserRequest(): UserRequesting
    {
        return $this->userRequest;
    }

    public function getProfesional(): string
    {
        return $this->profesional;
    }
}