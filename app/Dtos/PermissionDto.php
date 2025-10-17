<?php 

namespace App\Dtos;
use Illuminate\Http\Request;

class PermissionDto extends RoleDto{
    private array $roles;
    private string $area;
    public function __construct(Request $request){
        parent::__construct($request);
        $this->roles=$request->input('roles');
        $this->area=$request->input('area');
            
        
    }
    public function getRoles():array{
        return $this->roles;
    }
    public function toArray():array{
        return [
            'nombre'=>$this->name,
            'descripcion'=>$this->description,
            'activo'=>$this->active?'1':'0',
            'area'=>$this->area,
            
        ];
    }

}