<?php

namespace App\Files\Domain;


class Pdf
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
}