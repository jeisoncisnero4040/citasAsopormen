<?php


namespace App\Files\Domain;


class XML
{
    private string $xml;

    public function __construct(string $xml)
    {
        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }
}