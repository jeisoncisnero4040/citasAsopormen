<?php

namespace App\Http\Controllers;

use App\Dtos\PermissionDto;
use App\Dtos\RoleDto;
use App\Services\RolesAndPermissionsService;
use App\Utils\ResponseManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesAndPermissionsController extends Controller{
    private RolesAndPermissionsService $rolesAndPermissionsService;

    public function __construct(RolesAndPermissionsService $rolesAndPermissionsService){
        $this->rolesAndPermissionsService=$rolesAndPermissionsService;
    }
    public function getAllPermissions(Request $request):JsonResponse{
        return response()->json(
            $this->rolesAndPermissionsService->getAllPermissions(request:$request->all())
        );
    }
    public function getAllRoles(Request $request): JsonResponse
    {
        return response()->json(
            $this->rolesAndPermissionsService->getAllRoles($request->query())
        );
    }
    public function storeRole(Request $request){
        $dto=new RoleDto($request);
        return response()
                ->json(
                    $this->rolesAndPermissionsService->createRole($dto),201
                );

    }
    public function storePermission(Request $request){
        
        return response()
                ->json(
                    data:$this->rolesAndPermissionsService->createPermission(new PermissionDto($request)),
                    status:201
                );
    }

    public function all(Request $request){
        return response()
                ->json(
                    data:$this->rolesAndPermissionsService->getRolesAnsPermission()
                );
    }
    public function setPermissionRoles(Request $request){
        return response()
                ->json(
                    data:$this->rolesAndPermissionsService->setPermissionsToRole($request->all())
                );
    }

    public function deleteRol($roleId){
        return response()
            ->json(
                data:$this->rolesAndPermissionsService->deleteRoles(roleId:(int) $roleId)
            );
    }

    public function changueActiveRole($roleId){
        return response()
            ->json(
                data:$this->rolesAndPermissionsService->actuvateUnactivateRole(rolId:$roleId)
            );
    }
    public function toggleActivePermission(int $id){
        return response()
                ->json(
                    data:$this->rolesAndPermissionsService->ToggleActivePermission(idPermission:$id)
                );
    }

    public function deletePermission(int $id){
        return response()
                ->json(
                    data:$this->rolesAndPermissionsService->deletePermission(permissionId:$id)
                );
    }

}