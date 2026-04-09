<?php

namespace App\Interfaces;


interface TarifePort
{
    public function getTarife(string $code): ?array;
    public function getTarifeByEpsAndCovenant(string $epsCode, string $covenantCode): array;
    
}