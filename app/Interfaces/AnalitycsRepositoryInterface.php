<?php

namespace App\Interfaces;

interface AnalitycsRepositoryInterface {
    public function pqrsHistory(string $from, string $to):mixed;
}