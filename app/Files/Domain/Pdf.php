<?php

namespace App\Files\Domain;
use App\Files\Concrats\Adaptable;

class Pdf implements Adaptable
{
    private string $pdf;

    public function __construct(string $pdf)
    {
        $this->pdf = $pdf;
    }

    public function getPdf(): string
    {
        return $this->pdf;
    }
    public function getContent(): string
    {
        return $this->pdf;
    }
    public function getExtension(): string
    {
        return 'pdf';
    }
}