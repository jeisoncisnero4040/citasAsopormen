<?php

namespace App\Ports;
use App\Domain\ExternalResponse;

interface ClientsPort
{
    public function getData(string $documentNumber): ExternalResponse;
}