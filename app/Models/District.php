<?php

namespace App\Models;


class District{
    private string $code;
    private string $name;
    private string $dpto;
    private string $country;

   function __construct(
        string $code,
        string $name,
        string $dpto,
        string $country
    ) {
        $this->code=$code;
        $this->name=$name;
        $this->dpto=$dpto;
        $this->country=$country;
    }
    public static function fromArray(array $data):self{
        return new self(
            code:$data['codigo'],
            name:$data['nombre'],
            dpto:$data['departa'],
            country:$data['pais']

        );
    }
    public function getCode():string{return $this->code;}
    public function getDpto():string{return $this->dpto;}
    public function getName():string{return $this->name;}
    public function getCountry():string{return $this->country;}
}