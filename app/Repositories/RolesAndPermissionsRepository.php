<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsRepository extends BaseRepository{
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
    public function getPermissionsByRole(int $roleId){
        $query="select pm.nombre from permisos_mc pm
            inner join roles_permisos rp
            ON rp.permiso_id = pm.id
            WHERE rp.rol_id = ?";
        return self::sendQuery(query:$query,bindings:[$roleId]);
    }
}