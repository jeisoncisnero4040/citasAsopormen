<?php

namespace App\Interfaces;

use App\Domain\Code;
use App\Domain\DocumentClient;

interface ClientDocumentPort
{
    public function get(Code $clientCode): array;
    public function update(DocumentClient $documentClient): int;
}