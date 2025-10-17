<?php

namespace App\Models;

use stdClass;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ProfesionalModel implements JWTSubject
{
    private string $cedula;
    private string $nombre;
    private string $user;
    private int $rol;
    private int $diasHabilesEvo;
    private string $password;
    private int $tieneAgenda;
    private int $activo ;
    private int $password_changed;
    private string $email;
    private string $url_imagen;
    private string $specialty;
    private ?string $subRol;
    private string $celular;
    private string $direction;

    public function __construct(stdClass $profesional)
    {
        $this->cedula = $profesional->ecc;
        $this->nombre = $profesional->nombre;
        $this->user =$profesional->usuario;
        $this->rol = (int) $profesional->rol_id;
        $this->diasHabilesEvo = (int) $profesional->dias_habiles_evolucion;
        $this->password = $profesional->password;
        $this->tieneAgenda=$profesional->agenda;
        $this->activo=$profesional->eactivo;
        $this->password_changed=(int )$profesional->contrasenia_cambiada;
        $this->email=$profesional->email;
        $this->url_imagen=$profesional->url_imagen;
        $this->specialty=$profesional->especialidad;
        $this->subRol=$profesional->sub_rol;
        $this->celular=$profesional->cel;
        $this->direction=$profesional->direccion;
    }
    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getRol(): int
    {
        return $this->rol;
    }

    public function getDiasHabilesEvo(): int
    {
        return $this->diasHabilesEvo;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setCedula(string $cedula): void
    {
        $this->cedula = $cedula;
    }
    public function getEmail(){
        return $this->email;
    }
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setRol(int $rol): void
    {
        $this->rol = $rol;
    }

    public function setDiasHabilesEvo(int $dias): void
    {
        $this->diasHabilesEvo = $dias;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    public function isActive():bool{
        return $this->activo==1;
    }
    public function hasAgend():bool{
        return $this->tieneAgenda==1;
    }
    public function passwordChanged(){
        return $this->password_changed=='1';
    }
    public function getSubRols():array{
        if(!$this->subRol){return [];}
        return explode('-',$this->subRol);
    }


    public function toList(): array
    {
        return [
            'cedula' => $this->cedula,
            'nombre' => $this->nombre,
            'rol' => $this->rol,
            'dias_habiles_evo' => $this->diasHabilesEvo,
            'password_changed'=>$this->password_changed,
            'url_imagen'=>$this->url_imagen,
            'especialidad'=>$this->specialty,
            'usuario'=>$this->user,
            'celular'=>$this->celular,
            'direccion'=>$this->direction,
            'email'=>$this->email,
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->cedula; 
    }

    public function getJWTCustomClaims(): array
    {
        return ['rol' => $this->rol];
    }

}

    
