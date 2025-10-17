<?php

namespace App\Services;

use App\Dtos\PermissionDto;
use App\Dtos\RoleDto;
use App\Interfaces\RolesAndPermissionsRepositoryInterface;
use App\Mappers\RolsAndPermissionsMapper;
use App\Utils\ResponseManager;


class RolesAndPermissionsService{
    private ResponseManager $responseManager;
    private RolesAndPermissionsRepositoryInterface $rolesAndPermissionsRepository;

    public function __construct(ResponseManager $responseManager,
    RolesAndPermissionsRepositoryInterface $rolesAndPermissionsRepository)
    {
        $this->rolesAndPermissionsRepository=$rolesAndPermissionsRepository;
        $this->responseManager=$responseManager;
    }

    public function getAllPermissions(array $request){
        $onlyActives = isset($request['actives']) && $request['actives'] == 1;
        $permissions= $this->rolesAndPermissionsRepository->getAllPermissions(onlyActives:$onlyActives);
        return $this->responseManager->success(
           collect($permissions)
                ->map(function($permission){
                    return $permission->toArray();
                })
                ->toArray()
        );
    }
    public function getAllRoles(array $request){
        $onlyActives = isset($request['actives']) && $request['actives'] == 1;
        $roles=$this->rolesAndPermissionsRepository->getRolesByIds(onlyActives:$onlyActives);
        return $this->responseManager->success(
            collect($roles)
                ->map(function ($role) {
                    return $role->toArray();
                })
                ->toArray()
            );
        }

    public function createRole(RoleDto $role){
        $role->setActive(active:true);
        $newRoles=$this->rolesAndPermissionsRepository->createRole(role:$role);
        return $this->responseManager->created(
            $newRoles->toArray()
        );
    }
    public function createPermission(PermissionDto $permissionDto){
        $permissionDto->setActive(active:true);
        return $this->responseManager->created(
            $this->rolesAndPermissionsRepository->addPermission(permission:$permissionDto)                                        
        );
    }


    public function getRolesAnsPermission(): array{
        $roles = $this->rolesAndPermissionsRepository->getRolesByIds(onlyActives: false);
        return $this->returnRolesWithHisPermission($roles);
    }
    public function deleteRoles(int $roleId):array{
        
        return $this->responseManager->success(
            $this->rolesAndPermissionsRepository->deleteRole(rolId:$roleId)
        );
    }
    public function deletePermission(int $permissionId){
        return $this->responseManager->success(
            $this->rolesAndPermissionsRepository->deletePermission(permissionId:$permissionId)
        );
    }
    public function ToggleActivePermission(int $idPermission):array{

        $permissionUpdate=$this->rolesAndPermissionsRepository->activateUnactivatePermission(permissionId:$idPermission);
        return $this->responseManager->success(
            [$permissionUpdate[0]->toArray()]
        );
    }

    public function setPermissionsToRole(array $request){
        $role = $this->rolesAndPermissionsRepository->updatePermissionsRole(
            permissionsIds: $request['permissions'],
            rolId: $request['id']
        );
        return $this->returnRolesWithHisPermission($role);
    }
    public function actuvateUnactivateRole(int $rolId){
        $role=$this->rolesAndPermissionsRepository->activateUnactivateRole(roleId:$rolId);
        return $this->returnRolesWithHisPermission($role);

    }
    public function checkPermissionsRole(int $roleId, string ...$permissionNames): bool
    {
        return $this->rolesAndPermissionsRepository
            ->getPermissionsByRoleId($roleId, ...$permissionNames);

    }


    private function returnRolesWithHisPermission(array $roles){
        [$permissions, $rolesPermissions] = $this->getPermissionsAndRolesPermissions();

        return $this->responseManager->success(
            RolsAndPermissionsMapper::MapRolesAndPermissions(
                roles: $roles,
                permissions: $permissions,
                rolesPermissions: $rolesPermissions
            )
        );
    }

    private function getPermissionsAndRolesPermissions(): array{
        return [
            $this->rolesAndPermissionsRepository->getAllPermissions(onlyActives: false),
            $this->rolesAndPermissionsRepository->getRolesPermissions()
        ];
    }


}