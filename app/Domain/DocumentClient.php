<?php

namespace App\Domain;
use App\Domain\Code;
use App\Files\Domain\Pdf;

use App\Domain\KeyStorage;
use Illuminate\Support\Str;


class DocumentClient
{
    public function __construct(
        private Code $codeClient,
        
        private ?Pdf $pdf = null,
        private ?KeyStorage $keyStorage = null,
    ) {}
    public function getKeyStorage(): ?KeyStorage
    {
        return $this->keyStorage;
    }
    public function getCode(): Code
    {
        return $this->codeClient;
    }
    public function getPdf(): ?Pdf
    {
        return $this->pdf;
    }
    public function buildKey():void
    {
        $this->keyStorage = new KeyStorage('Documentos/'.  $this->codeClient->getCode().'/'.'documentos'.'/'.Str::uuid().'.pdf');
        
    }
    public static function createFromArray(array $data): DocumentClient
    {
        $codeClient = new Code($data['codigo']);
        $key = isset($data['url']) ? new KeyStorage($data['url']) : null;
        $documentClient = new DocumentClient(
            codeClient: $codeClient,
            keyStorage: $key
        );
        return $documentClient;
    }
}