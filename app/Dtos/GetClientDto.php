<?php

namespace App\Dtos;

use App\Exceptions\CustomExceptions\BadRequestException;

class GetClientDto
{
    private string $text;
    private bool $onlyActives;
    public function __construct(string $text, bool $onlyActives = false)
    {
        $this->text = trim($text);
        $this->onlyActives = $onlyActives;



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
            text: $data['param'] ?? '',
            onlyActives: isset($data['onlyActives']) ? filter_var($data['onlyActives'], FILTER_VALIDATE_BOOLEAN) : false
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

    public function isOnlyActives(): bool
    {
        return $this->onlyActives;
    }
}