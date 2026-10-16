<?php


namespace App\Repositories;
use App\Domain\Code;
use App\Domain\DocumentClient;
use App\Interfaces\ClientDocumentPort;

class ClientDocumentRepository extends BaseRepository implements ClientDocumentPort
{
    public function get(Code $clientCode): array
    {
        $query = "SELECT codigo,
                        patch_documento_asp AS url
                FROM cliente2
                WHERE codigo = ?";
        $result = $this->sendQuery($query, [$clientCode->getCode()]);
        return array_map(fn($item) => DocumentClient::createFromArray((array)$item), $result);
    }
    public function update(DocumentClient $documentClient): int
    {
        $query = "UPDATE cliente2 
                  SET patch_documento_asp = ?
                  WHERE codigo = ?";
        return $this->sendQuery($query, [
            $documentClient->getKeyStorage()?->getKey(),
            $documentClient->getCode()->getCode()],
        'update');
    }
}