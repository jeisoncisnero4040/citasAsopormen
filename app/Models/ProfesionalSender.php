<?php
namespace App\Models;

use App\Interfaces\Serializable;

class ProfesionalSender implements Serializable{
    private string $name;
    private string $code;


    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['nombre'] ?? '',
            code: $data['cod'] ?? ''
        );
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getCode(): string
    {        return $this->code;
    }

    public function toArray(): array
    {
        return ['cod'=>$this->code,
            'nombre'=>$this->name
        ];
    }


}