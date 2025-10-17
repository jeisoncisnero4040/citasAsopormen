<?php

namespace App\Models;

use stdClass;

class ClientModel
{
    private string $nombre;
    private string $cedula;
    private int $edad;
    private string $entidad;
    private string $cod;
    private string $city;
    private ?array $procedipros;

    public function __construct(stdClass $client)
    {
        $this->nombre  = $client->nombre ?? '';
        $this->cedula  = $client->cedula ?? '';
        $this->edad    = $client->edad ?? 0;
        $this->entidad = $client->entidad ?? '';
        $this->cod     = $client->cod ?? '';
        $this->city    = $client->ciudad??''; 
    }
    public function setProcedipros(array $procedipros):void{
        $this->procedipros=$procedipros;
    }
    public function toArray(): array
    {
        return [
            'name'  => $this->nombre,
            'cedula'  => $this->cedula,
            'age'    => $this->edad,
            'entity' => $this->entidad,
            'cod'     => $this->cod,
            'procedipros'  => $this->procedipros,
            'city'      =>$this->city
        ];
    }
}
