<?php

namespace App\Services;

use App\Repositories\RolesAndPermissionsRepository;

class RolesAndPermissionsService{
    private RolesAndPermissionsRepository $repo;

    public function __construct(RolesAndPermissionsRepository $repo)
    {
        $this->repo=$repo;
    }
    public function checkPermissionsRole(int $roleId, string ...$permissionNames): bool
    {
        return $this->repo
            ->getPermissionsByRoleId($roleId, ...$permissionNames);

    }

}