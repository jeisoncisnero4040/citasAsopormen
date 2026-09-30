<?php

namespace App\Files\Domain;


class JSON
{
    private array $json;

    public function __construct(array $json)
    {
        $this->json = $json;
    }

    public function getJson(): array
    {
        return $this->json;
    }
    public function setJson(array $json): void
    {
        $this->json = $json;
    }

}