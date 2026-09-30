<?php

namespace App\Files\Domain;

use setasign\Fpdi\Fpdi;

class JoinerPdfEngine
{
    public function create(): Fpdi
    {
        return new Fpdi();
    }
}