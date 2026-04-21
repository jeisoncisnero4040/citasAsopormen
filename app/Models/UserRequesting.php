<?php

namespace App\Models;

class UserRequesting
{
    private const DEVELOPER_ROLE_ID = 19;
    private const ADMIN_ROLE_ID = 1;
    private const ROLES_USER = [22,23,24];
    private string $cedula;
    private string $username;
    private int $role;

    public function __construct(string $cedula, string $username, int $role)
    {
        $this->cedula = $cedula;
        $this->username = $username;
        $this->role = $role;

    }
    public static function fromArray(array $data): self
    {
        return new self(
            cedula: $data['userRequestCedula'] ?? '',
            username: trim($data['usernameRequest'] ?? ''),
            role: $data['rol_id'] ?? 0
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

    public function getRole(): int
    {

        return $this->role;
    }
    public function isUser(): bool
    {
        return in_array($this->role, self::ROLES_USER, true);
    }
    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN_ROLE_ID;
    }

    public function isDeveloper(): bool
    {
        logger()->info("User role: " . $this->role);
        return $this->role === self::DEVELOPER_ROLE_ID;
    }
    public function toAuditData(): array
    {
        return [
            'cedula' => $this->cedula, 
            'username' => $this->username,

        ];
    } 
}