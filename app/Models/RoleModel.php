<?php

namespace App\Models;

use stdClass;

class RoleModel
{
    private int $rolId;
    private string $role;
    private string $roleDescription;
    private bool $active;
    private string $subRol;

    

    public function __construct(stdClass $row)
    {
        $this->rolId = (int) $row->role_id;
        $this->role = $row->role_name;
        $this->roleDescription = $row->role_description;
        $this->active = (bool) $row->role_active;
        $this->subRol=$row->sub_rol;
    }

    
    public function getRolId(): int { return $this->rolId; }
    public function getRole(): string { return $this->role; }
    public function getRoleDescription(): string { return $this->roleDescription; }
    public function isActive(): bool { return $this->active; }

    public function getSubRol():string {return $this->subRol;}

    public function toArray(): array
    {
        return [
            "id"          => $this->rolId,
            "nombre"        => $this->role,
            "descripcion" => $this->roleDescription,
            "activo"      => $this->active,
            "subRol"          => $this->subRol,
        ];
    }
}

