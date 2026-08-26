<?php

namespace App\Domain;

class ProvisionalAuthCode
{
    const PREFIX = 'PROV';
    public function __construct(private string $authConsecutive)
    { }
    public function getCode(): string
    {
        return self::PREFIX . $this->authConsecutive;
    }
}