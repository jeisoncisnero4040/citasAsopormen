<?php

namespace App\Dtos;

use InvalidArgumentException;

class CreateAgreementsDto
{
    public function __construct(

    ) {}

    public static function fromArray(array $data): self
    {


        return new self(

        );
    }
}