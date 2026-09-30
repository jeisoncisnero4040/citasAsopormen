<?php
namespace App\Domain;

class Code
{
    public function __construct(
        private readonly string $code
    ){}
    public static function fromString(string $code): self
    {
        return new self($code);
    }
    public function getCode(): string
    {
        return $this->code;
    }
}