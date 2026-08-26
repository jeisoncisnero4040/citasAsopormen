<?php

namespace App\Domain;
use App\Exceptions\CustomExceptions\ServerErrorException;

class KeyAuthCup
{
    public function __construct(
        private string $autorizacion,
        private string $cupCode
    )
    {
    }
    public function getKey(): string
    {
        return $this->autorizacion.'|||'.$this->cupCode;
    }
    public static function fromKey(string $key): self
    {
        [$autorizacion, $cupCode] = explode('|||', $key);
        if (!isset($autorizacion) || !isset($cupCode)) {
            throw new ServerErrorException('Error al distinguir la clave de autorizacion y codigo Cup');
        }
        return new self($autorizacion, $cupCode);
    }
    public function equals(self $other): bool
    {
        return $this->autorizacion === $other->autorizacion && $this->cupCode === $other->cupCode;
    }
}