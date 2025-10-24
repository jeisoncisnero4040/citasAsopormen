<?php

namespace App\Dtos;

use App\Exceptions\CustomExceptions\BadRequestException;


class UpdateProDto{
    private ?string $cedula;
    private ?string $direction;
    private ?string $cel;
    private ?string $email;
    private ?string $urlAvatar;

    public function __construct(array $data)
    {
        $this->cedula= $data['cedula']??null;
        $this->direction=$data['direccion']??null;
        $this->cel=$data['celular']??null;
        $this->email=$data['email']??null;

        $this->validate();

    }
    public function getUser():string{return $this->cedula;}
    public function setUrlAvatar(string $newUrl){$this->urlAvatar=$newUrl;}

    public function toArrayPersistence(): array
    {
        $data = [
            'edirecc'    => $this->direction,
            'etel'       => $this->cel,
            'email'      => $this->email,
            'url_imagen' => $this->urlAvatar,
        ];

        return array_filter($data, fn($value) => !is_null($value));
    }

    private function Validate():void{
        if(!$this->cedula){
            throw new BadRequestException("No se ha suministrado ningun profesional a actualizar",400);
        }
        if(!$this->direction
            && !$this->email && !$this->cel
        ){
            throw new BadRequestException("Al menos un parametro a actualizar es obligatorio",400);
        }
    }
}