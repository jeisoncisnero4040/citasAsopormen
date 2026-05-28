<?php

namespace App\Dtos;

use InvalidArgumentException;

class CreateCapacitacionMediaDto
{
    public function __construct(

    ) {}

    public static function fromArray(array $data): self
    {


        return new self(

        );
    }
}