<?php

namespace App\Interfaces;

use App\Dtos\PermissionDto;
use App\Models\RoleModel;
use App\Dtos\RoleDto;

interface RolesAndPermissionsRepositoryInterface
{

    public function createRole(RoleDto $role): RoleModel;
    public function addPermission(PermissionDto $permission):void;
    public function updatePermissionsRole(array $permissionsIds,int $rolId): array;
    public function getPermissionsByRoleId(int $roleId, string ...$permissionList): bool;
    public function getRolesByIds(array $rolesIds = [],bool $onlyActives=true):array;
    public function getPermissionsByIds(array $permissionsIds = [],bool $onlyActives=true):array;
    public function getAllPermissions(bool $onlyActives=true):array;
    public function getAllRoles(bool $onlyActives=true):array;
    public function deleteRole(int $rolId):array;
    public function deletePermission(int $permissionId):array;
    public function getRolesPermissions():array;
    public function activateUnactivateRole(int $roleId):array;
    public function activateUnactivatePermission(int $permissionId):array;
}
