<?php

namespace App\Files\Domain;

use setasign\Fpdi\Fpdi;

class PdfEngine
{
    public function create(): Fpdi
    {
        return new Fpdi();
    }
}