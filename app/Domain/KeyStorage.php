<?php

namespace App\Domain;
use App\Interfaces\Keyable;


class KeyStorage implements Keyable
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}