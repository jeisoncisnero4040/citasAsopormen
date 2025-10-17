<?php
namespace App\Mappers;

use App\Models\RoleModel;

class RolsAndPermissionsMapper{
    public static function MapRolesAndPermissions(
        array $roles,
        array $permissions,
        array $rolesPermissions
    ){
        $result = [];
        foreach ($roles as $role) {
            $result[] = self::mapRole($role, $permissions, $rolesPermissions);
        }
        return $result;
    }

    private static function mapRole($role, array $permissions, array $rolesPermissions): array {
        return [
            'nombre'   => $role->getRole(),
            'id'       => $role->getRolId(),
            'activo'   => $role->isActive(),
            'permisos' => self::mapPermissions($role, $permissions, $rolesPermissions)
        ];
    }

    private static function mapPermissions($role, array $permissions, array $rolesPermissions): array {
        $mapped = [];
        foreach ($permissions as $permission) {
            $mapped[] = self::mapPermission($role, $permission, $rolesPermissions);
        }
        return $mapped;
    }

    private static function mapPermission($role, $permission, array $rolesPermissions): array {
        $rp = collect($rolesPermissions)->first(function ($rp) use ($role, $permission) {
            return (int)$rp->rol_id === $role->getRolId()
                && (int)$rp->permiso_id === $permission->getId();
        });

        return [
            'name'   => $permission->getNombre(),
            'value'  => $rp ? 1 : 0,
            'activo' => $permission->isActive() ?? 1,
            'id'     => $permission->getId()
        ];
    }
}
