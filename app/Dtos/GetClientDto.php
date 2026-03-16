<?php

namespace App\Dtos;

use App\Exceptions\CustomExceptions\BadRequestException;

class GetClientDto
{
    private string $text;
    public function __construct(string $text)
    {
        $this->text = trim($text);



        if (empty($this->text)) {
            throw new BadRequestException(
                "El parametro a buscar no debe estar vacio",
                400
            );
        }
        
    }
    public static function fromArray(array $data): self
    {
        return new self(
            text: $data['param'] ?? ''
        );
    }
    public function isOnlyNumericText(): bool
    {
        return ctype_digit($this->text);
    }

    public function startWithZeros(): bool
    {
        return str_starts_with($this->text, '0');
    }

    public function getText(): string
    {
        return $this->text;
    }

}