<?php

namespace App\Models;

use stdClass;

class UtilityModel {
    private string $code;
    private string $description;
    private string $type;

    public function __construct(stdClass $utility)
    {
        $this->code = $utility->codigo;
        $this->description = $utility->descripcion;
        $this->type = $utility->tipo;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
        ];
    }
}
