<?php

namespace App\Models;

class UserRequesting
{
    private string $cedula;
    private string $username;

    public function __construct(string $cedula, string $username)
    {
        $this->cedula = $cedula;
        $this->username = $username;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            cedula: $data['userRequestCedula'] ?? '',
            username: trim($data['usernameRequest'] ?? '')
        );
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    public function toAuditData(): array
    {
        return [
            'cedula' => $this->cedula, 
            'username' => $this->username,
        ];
    } 
}