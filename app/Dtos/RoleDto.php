<?php

namespace App\Dtos;
use Illuminate\Http\Request;

class RoleDto{
    protected string $name;
    protected string $description;
    protected bool|null $active;
    protected string|null $subRol;
    protected array|null $permissions;


    public function __construct(Request $request){
        $this->name=$request->input('name');
        $this->description=$request->input('description');
        $this->permissions=$request->input('permission',null);
        $this->subRol=$request->input('subRol',null);
        $this->active=null;
    }
    public function setActive(bool $active):void{$this->active=$active;}
    public function getDescrition():string{return $this->description;}
    public function getName():string{return $this->name;}
    public function getPermissions():array|null{return $this->permissions;}

    public function toArray():array{
        return [
            'nombre'=>$this->name,
            'descripcion'=>$this->description,
            'activo'=>$this->active?'1':'0',
            'sub_rol'=>$this->subRol,
            
        ];
    }

}