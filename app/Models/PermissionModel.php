<?php

namespace App\Models;

use stdClass;

class PermissionModel
{
    private int $id;
    private string $nombre;
    private string $area;
    private string $descripcion;
    private bool $active;



    public function __construct(stdClass $data)
    {
        $this->id = $data->id ?? 0;
        $this->nombre=$data->nombre;
        $this->area=$data->area;
        $this->descripcion=$data->descripcion;
        $this -> active =$data->activo=='1'?true:false;


    }

    
    public function getId(): int { return $this->id; }
    public function getNombre():string{return $this->nombre;}
    public function isActive():bool{return $this->active;}
    
    public function toArray(): array{
        return [
            'id'                   => $this->id,
            'nombre'               => $this->nombre,
            'area'                 => $this->area,
            'descripcion'          => $this->descripcion,
            'activo'               => $this->active,

        ];      
    }
    public function toString(){
        return "prermiso con id " .$this->id;
    }
}
