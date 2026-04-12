<?php

namespace App\Services;
use App\Ports\ClientsPort;
use App\Domain\ExternalResponse;

class FakeUsaerService implements ClientsPort
{
    public function getData(string $documentNumber): ExternalResponse
    {
        return ExternalResponse::fromSuccess("Fake user data for document number: $documentNumber", [
            'name' => 'Jeison Olegario Cisneros Figueroa',
            'document_number' => $documentNumber,
            'age'  => 30,
            'id' => '123456789',
        ] );
    }
}