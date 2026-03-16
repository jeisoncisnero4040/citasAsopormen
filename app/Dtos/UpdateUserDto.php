<?php

namespace App\Dtos;

use App\Interfaces\Persistable;

class UpdateUserDto implements Persistable
{
    private string $celular;
    private string $email;
    private ?string $ocupacion;
    private string $direccion;
    private string $barrio;
    private ?string $municipio;

    private string $responsable;
    private string $celular_responsable;
    private string $parentezco_responsable;
    private string $password;

    private array $payload;

    public function __construct(array $data)
    {
        $this->celular = $data['celular'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->ocupacion = $data['ocupacion'] ?? null;
        $this->direccion = $data['direccion'] ?? '';
        $this->barrio = $data['barrio'] ?? '';
        $this->municipio = $data['municipio'] ?? null;

        $this->responsable = $data['responsable'] ?? '';
        $this->celular_responsable = $data['celular_responsable'] ?? '';
        $this->parentezco_responsable = strtoupper($data['parentezco_responsable'] ?? '');
        $this->password = $data['password']??'';

        $this->payload=$data;
    }
    public function getPwd():string{
        return $this->password;
    }

    public function getCelular(): string
    {
        return $this->celular;
    }

    public function setCelular(string $v): void
    {
        $this->celular = $v;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $v): void
    {
        $this->email = $v;
    }

    public function getOcupacion(): ?string
    {
        return $this->ocupacion;
    }

    public function setOcupacion(?string $v): void
    {
        $this->ocupacion = $v;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function setDireccion(string $v): void
    {
        $this->direccion = $v;
    }

    public function getBarrio(): string
    {
        return $this->barrio;
    }

    public function setBarrio(string $v): void
    {
        $this->barrio = $v;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(?string $v): void
    {
        $this->municipio = $v;
    }

    public function getResponsable(): string
    {
        return $this->responsable;
    }

    public function setResponsable(string $v): void
    {
        $this->responsable = $v;
    }

    public function getCelularResponsable(): string
    {
        return $this->celular_responsable;
    }

    public function setCelularResponsable(string $v): void
    {
        $this->celular_responsable = $v;
    }

    public function getParentezcoResponsable(): string
    {
        return $this->parentezco_responsable;
    }

    public function setParentezcoResponsable(string $v): void
    {
        $this->parentezco_responsable = strtoupper($v);
    }

    public function Payload(): array
    {
        return $this->payload;
    }
    public function toPersistenceArray(): array
    {
        $array= [
            'direcc'=>$this->direccion,
            'barrio'=>$this->barrio,
            'cel'=>$this->celular,
            'email'=>$this->email,
            'telacompañante'=>$this->celular_responsable,
            'nombreresponsable'=>$this->responsable,
            'parentresponsable'=>$this->parentezco_responsable

        ];
        if ($this->municipio) {
            $array = array_merge($array, ['cod_ciudad' => $this->municipio]);
        }

        if ($this->ocupacion) {
            $array = array_merge($array, ['ocupacion' => $this->ocupacion]);
        }
        return $array;
    }
}
