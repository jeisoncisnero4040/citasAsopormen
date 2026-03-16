<?php

namespace App\Dtos;


class GetTicketsDto{
    private ?string $cedula;

    public function  __construct(?string $cedula)
    {
        $this->cedula = $cedula;
    }
    public static function fromArray(array $data){
        return new self(
            cedula : $data['cedula']??null
        );
        
    }
    public function getCedula():?string{
        return $this->cedula;
    }

}