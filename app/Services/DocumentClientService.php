<?php
namespace App\Services;

use App\Domain\Code;

use App\Domain\DocumentClient;
use App\Domain\KeyStorage;
use App\Interfaces\StorageInterface;
use App\Interfaces\ClientDocumentPort;

class DocumentClientService
{
    public function __construct(
        private StorageInterface $storage,
        private ClientDocumentPort $repo
    )
    {
    }
    public function update(DocumentClient $documentClient): void
    {

        $documentClient->buildKey();
        $oldDocument = $this->get($documentClient->getCode());
        $this->repo->update($documentClient);
        $this->storage->put($documentClient->getKeyStorage(), $documentClient->getPdf()->getContent());
        if($oldDocument?->getKeyStorage()?->getKey()){
            $this->storage->destroy($oldDocument->getKeyStorage()->getKey());
        }
    }
    public function get(Code $clientCode): ?DocumentClient
    {
        $documents = $this->repo->get($clientCode);
        return $documents[0] ?? null;
    }
}