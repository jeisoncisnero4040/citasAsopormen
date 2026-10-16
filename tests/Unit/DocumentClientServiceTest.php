<?php

namespace Tests\Unit;

use App\Domain\Code;
use App\Domain\DocumentClient;
use App\Domain\KeyStorage;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Files\Domain\Pdf;
use App\Interfaces\ClientDocumentPort;
use App\Interfaces\Keyable;
use App\Interfaces\StorageInterface;
use App\Services\DocumentClientService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DocumentClientServiceTest extends TestCase
{
    public function test_it_uploads_the_new_document_updates_the_client_and_removes_the_old_document(): void
    {
        $clientCode = new Code('0000000001');
        $oldDocument = new DocumentClient(
            codeClient: $clientCode,
            keyStorage: new KeyStorage('old-document.pdf')
        );
        $documentClient = new DocumentClient(
            codeClient: $clientCode,
            pdf: new Pdf('new pdf content')
        );

        $storage = $this->createMock(StorageInterface::class);
        $repository = $this->createMock(ClientDocumentPort::class);
        $repository->expects(self::once())
            ->method('get')
            ->with($clientCode)
            ->willReturn([$oldDocument]);
        $storage->expects(self::once())
            ->method('put')
            ->with(
                self::callback(fn (Keyable $key) => str_starts_with(
                    $key->getKey(),
                    'Documentos/0000000001/documentos/'
                )),
                'new pdf content'
            )
            ->willReturn(true);
        $repository->expects(self::once())
            ->method('update')
            ->with($documentClient)
            ->willReturn(1);
        $storage->expects(self::once())
            ->method('destroy')
            ->with('old-document.pdf')
            ->willReturn(true);

        (new DocumentClientService($storage, $repository))->update($documentClient);
    }

    public function test_it_does_not_update_the_client_when_uploading_the_new_document_fails(): void
    {
        $clientCode = new Code('0000000001');
        $documentClient = new DocumentClient(
            codeClient: $clientCode,
            pdf: new Pdf('new pdf content')
        );
        $storage = $this->createMock(StorageInterface::class);
        $repository = $this->createMock(ClientDocumentPort::class);
        $repository->expects(self::once())
            ->method('get')
            ->with($clientCode)
            ->willReturn([
                new DocumentClient($clientCode, keyStorage: new KeyStorage('old-document.pdf')),
            ]);
        $storage->expects(self::once())
            ->method('put')
            ->willReturn(false);
        $repository->expects(self::never())->method('update');
        $storage->expects(self::never())->method('destroy');

        try {
            (new DocumentClientService($storage, $repository))->update($documentClient);
            self::fail('Expected a storage exception.');
        } catch (ServerErrorException $exception) {
            self::assertSame('No se pudo guardar el nuevo documento del usuario.', $exception->getMessage());
        }
    }

    public function test_it_removes_the_new_document_when_updating_the_client_fails(): void
    {
        $clientCode = new Code('0000000001');
        $documentClient = new DocumentClient(
            codeClient: $clientCode,
            pdf: new Pdf('new pdf content')
        );
        $storage = $this->createMock(StorageInterface::class);
        $repository = $this->createMock(ClientDocumentPort::class);
        $repository->expects(self::once())
            ->method('get')
            ->willReturn([
                new DocumentClient($clientCode, keyStorage: new KeyStorage('old-document.pdf')),
            ]);
        $storage->expects(self::once())->method('put')->willReturn(true);
        $repository->expects(self::once())
            ->method('update')
            ->willThrowException(new RuntimeException('Database update failed.'));
        $storage->expects(self::once())
            ->method('destroy')
            ->with(self::callback(fn (string $key) => str_starts_with(
                $key,
                'Documentos/0000000001/documentos/'
            )))
            ->willReturn(true);

        try {
            (new DocumentClientService($storage, $repository))->update($documentClient);
            self::fail('Expected the repository exception.');
        } catch (RuntimeException $exception) {
            self::assertSame('Database update failed.', $exception->getMessage());
        }
    }
}
