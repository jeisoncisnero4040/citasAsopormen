<?php

namespace App\Repositories;

use App\Dtos\PermissionDto;
use App\Dtos\RoleDto;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Exceptions\CustomExceptions\PersistenceError;
use App\Interfaces\RolesAndPermissionsRepositoryInterface;
use App\Models\PermissionModel;
use App\Models\RoleModel;
use App\Services\LogsService;
use Hamcrest\Arrays\IsArray;
use Illuminate\Support\Facades\DB;


final class RolesAndPermissionsRepository extends BaseRepository implements RolesAndPermissionsRepositoryInterface{
    public function createRole(RoleDto $role): RoleModel
    {
        $infoNewRole = $role->toArray();
        $placeholder = self::makePlaceholders($infoNewRole);
        $colums = self::makeColumns($infoNewRole);
        $values = self::makeValues($infoNewRole);

        $query = "INSERT INTO roles_mc ({$colums}) VALUES ({$placeholder})";

        $permissionList = $role->getPermissions();
        $numPermissions = $permissionList?count($permissionList):0;

        $placeholdersPermissions = implode(', ', array_fill(0, $numPermissions, '(?, ?)'));
        $querySavePermission = "INSERT INTO roles_permisos (rol_id, permiso_id) VALUES {$placeholdersPermissions}";

        try {
            DB::beginTransaction();
            DB::insert($query, $values);

            $id = DB::getPdo()->lastInsertId();

            $valuesPermissions = collect($permissionList)
                ->flatMap(fn($permisoId) => [$id, $permisoId])
                ->toArray();

            if ($numPermissions > 0) {
                DB::insert($querySavePermission, $valuesPermissions);
            }

            DB::commit();
            return $this->getRolesByIds(rolesIds:[$id])[0];

        } catch (\Exception $e) {
            DB::rollBack();
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'roles y permisos',
                logsService: app(LogsService::class)
            );
        }
    }
    
    public function addPermission(PermissionDto $permission): void{
        $infoNewPermission = $permission->toArray();
        $placeholder = self::makePlaceholders($infoNewPermission);
        $colums = self::makeColumns($infoNewPermission);
        $values = self::makeValues($infoNewPermission);

        $query = "INSERT INTO permisos_mc ({$colums}) VALUES ({$placeholder})";

        $rolesIds = $permission->getRoles();
        $numRoles = $rolesIds?count($rolesIds):0;

        $placeholdersPermissions = implode(', ', array_fill(0, $numRoles, '(?, ?)'));
        $querySavePermission = "INSERT INTO roles_permisos (permiso_id, rol_id) VALUES {$placeholdersPermissions}";

        try {
            DB::beginTransaction();
            DB::insert($query, $values);

            $id = DB::getPdo()->lastInsertId();



            if ($numRoles > 0) {
                $valuesPermissions = collect($rolesIds)
                    ->flatMap(fn($rolId) => [$id, $rolId])
                    ->toArray();
                DB::insert($querySavePermission, $valuesPermissions);
            }

            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'roles y permisos',
                logsService: app(LogsService::class)
            );
        }
    }
    public function getPermissionsByRoleId(int $roleId, string ...$permissionList): bool
    {
        $placeholders = implode(', ', array_fill(0, count($permissionList), '?'));

        $query = "SELECT CASE WHEN EXISTS (
                SELECT 1
                FROM roles_permisos rp
                INNER JOIN permisos_mc p ON rp.permiso_id = p.id
                WHERE rp.rol_id = ?
                AND p.nombre IN ($placeholders)
            ) THEN 1 ELSE 0 END AS permitido";

        $bindings = array_merge([$roleId], $permissionList);

        $result = DB::select($query, $bindings);

        return !empty($result) && $result[0]->permitido == 1;
    }
    public function getRolesByIds(array $rolesIds = [],bool $onlyActives=true):array{
        $query = "SELECT 
                id AS role_id,
                nombre AS role_name,
                descripcion AS role_description,
                activo AS role_active,
                sub_rol

            FROM roles_mc 

        ";

        $bindings = [];

        if (!empty($rolesIds)) {
            $placeholders = implode(', ', array_fill(0, count($rolesIds), '?'));
            $query .= "WHERE id IN ($placeholders)";
            $bindings = $rolesIds;
        }
        if($onlyActives && empty($rolesIds) ){
            $query=$query."WHERE activo = 1";
        }
        if($onlyActives){
            $query=$query."AND activo = 1";
        }

        $roles= self::sendQuery(query: $query, bindings: $bindings);
        return collect($roles)->mapInto(RoleModel::class)->toArray();
    }
    public function getPermissionsByIds(array $permissionsIds = [], bool $onlyActives = true): array
    {
        $query = "SELECT id, nombre, area, descripcion, activo 
                FROM permisos_mc";
        $bindings = [];
        $conditions = [];

        if (!empty($permissionsIds)) {
            $placeholders = implode(', ', array_fill(0, count($permissionsIds), '?'));
            $conditions[] = "id IN ($placeholders)";
            $bindings = array_merge($bindings, $permissionsIds);
        }

        if ($onlyActives) {
            $conditions[] = "activo = 1";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $permissions = self::sendQuery(query: $query, bindings: $bindings);
        return collect($permissions)
                ->mapInto(PermissionModel::class)
                ->toArray();
    }
    public function getAllRoles(bool $onlyActives = true): array
    {
        $query="SELECT id,nombre FROM permisos_mc";
        if($onlyActives){
            $query=$query." WHERE activo = 1";
        }
        $permisions=self::sendQuery(query:$query);
        if(empty($permisions)){
            throw new NotFoundException("No se han encontrado de Registros de Pernisos Guardados",404);
        }
        return collect($permisions)->mapInto(PermissionModel::class)->toArray();
    }

    public function getAllPermissions(bool $onlyActives=true):array{
        $query="SELECT id,nombre,area,descripcion,activo,area FROM permisos_mc";
        if($onlyActives){
            $query=$query." WHERE activo = 1";
        }
        $permisions=self::sendQuery(query:$query);
        if(empty($permisions)){
            throw new NotFoundException("No se han encontrado de Registros de Pernisos Guardados",404);
        }
        return collect($permisions)->mapInto(PermissionModel::class)->toArray();
    }


    public function updatePermissionsRole(array $permissionsIds,int $rolId): array
    {
        $hasNewPermissions=count($permissionsIds)>0;
        try {
            DB::beginTransaction();
            DB::delete("DELETE FROM roles_permisos WHERE rol_id = ?", [$rolId]);
            if($hasNewPermissions){    
                $placeholders = implode(', ', array_fill(0, count($permissionsIds), '(?, ?)'));
                $query = "INSERT INTO roles_permisos (rol_id, permiso_id) VALUES {$placeholders}";
                $bindings = collect($permissionsIds)
                    ->flatMap(fn($permisoId) => [$rolId, $permisoId])
                    ->toArray();

                DB::insert($query, $bindings);
            }
            DB::commit();
            return $this->getRolesByIds(rolesIds:[$rolId],onlyActives:false);

        } catch (\Exception $e) {
            DB::rollBack();
            throw new PersistenceError(
                message: $e->getMessage(),
                action: 'actualizar permisos de rol',
                logsService: app(LogsService::class)
            );
        }
    }
    public function deleteRole(int $rolId): array{
        return $this->deleteRolOrPermission(id: $rolId, isRol: true);
    }

    public function deletePermission(int $permissionId): array{
        return $this->deleteRolOrPermission(id: $permissionId, isRol: false);

    }
    public function getRolesPermissions():array{
        return self::sendQuery("SELECT * FROM roles_permisos");
    }
    public function activateUnactivateRole(int $roleId):array{
        DB::update(query:"UPDATE roles_mc 
            SET activo = CASE WHEN activo = 1 THEN 0 ELSE 1 END
            WHERE id =?",
            bindings:[$roleId]

        );
        return $this->getRolesByIds(rolesIds:[$roleId],onlyActives:false);
    }
    public function activateUnactivatePermission(int $permissionId): array
    {
        self::sendQuery(query:"UPDATE permisos_mc
                SET activo = CASE WHEN activo = 1 THEN 0 ELSE 1 END
                WHERE id =?",
                bindings:[$permissionId],
                typeConsult:'update');
        return  $this->getPermissionsByIds(permissionsIds:[$permissionId],onlyActives:false);
    }


    private function deleteRolOrPermission(int $id, bool $isRol): array
    {
        $tableName   = $isRol ? 'roles_mc' : 'permisos_mc';
        $idToFilter  = $isRol ? 'rol_id' : 'permiso_id';

        $deleteMainQuery = "DELETE FROM {$tableName} WHERE id = ?";
        $deletePivotQuery = "DELETE FROM roles_permisos WHERE {$idToFilter} = ?";
        $bindings = [$id];

        try {
            DB::beginTransaction();

            $pivotDeleted = DB::delete(query: $deletePivotQuery, bindings: $bindings);
            $mainDeleted  = DB::delete(query: $deleteMainQuery, bindings: $bindings);

            DB::commit();

            return [$pivotDeleted ,$mainDeleted];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new PersistenceError(
                message: $e->getMessage(),
                action: $isRol ? 'eliminar rol' : 'eliminar permiso',
                logsService: app(LogsService::class)
            );
        }
    }





}