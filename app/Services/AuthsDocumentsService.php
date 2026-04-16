<?php

namespace App\Services;

use App\Services\QueueService;
use App\Dtos\CreateAuthsDocumentsDto;
use App\Dtos\GetAuthsDocumentsDto;
use App\Interfaces\AuthsDocumentsPort;
use App\Commands\AuthsDocumentsCommand;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\UserRequesting;
use Illuminate\Http\UploadedFile;
use App\Models\AuthsDocumentsViewModel;


class AuthsDocumentsService extends BaseService
{
    private AuthsDocumentsPort $repository;
    private StorageService $storage;
    protected QueueService $queueService;

    public function __construct(
        AuthsDocumentsPort $repository,
        QueueService $queueService,
        StorageService $storage
    ) {
        parent::__construct($queueService);
        $this->repository = $repository;
        $this->queueService = $queueService;
        $this->storage = $storage;
    }
    /**
     * @param CreateAuthsDocumentsDto $dto
     * @param UserRequesting $user
     * @param UploadedFile $file
     * @return AuthsDocumentsViewModel[]
     */
    public function create(CreateAuthsDocumentsDto $dto,UserRequesting $user, UploadedFile $file ): array
    {
        $documentCommand = new AuthsDocumentsCommand(
            n_autoriza: $dto->getNAutoriza(),
            documentTypeId: $dto->getDocumentTypeId(),
            cedulaUser: $dto->getCedulaUser(),
            epsCode: $dto->getEpsCode(),
            created_At: now()->toDateTimeString(),
            clientCode: $dto->getClientCode(),
            nameDocument: $dto->getNameDocument()

        );
        $key=$this->storage->store($file, $documentCommand->buildkeyDocument());
        $documentCommand->setKeyDocument($key);
        $newFileId = $this->repository->create($documentCommand);
        $response = $this->get(GetAuthsDocumentsDto::fromId($newFileId));
        $this->dispatchToQueue(
            $documentCommand->messaggeAudit($user),
            $user
        );

        return $this->singUrl($response);
    }
    /**
     * @param GetAuthsDocumentsDto $dto
     * @return AuthsDocumentsViewModel[]
     */
    public function get(GetAuthsDocumentsDto $dto): array
    {
        
        //if(!$dto->isValid()) {
           // throw new BadRequestException('Los campos obligatorios no pueden estar vacíos.',400);
        //}
        $response = $this->repository->get($dto);

        if(empty($response)) {
            throw new BadRequestException('No se encontraron documentos con los criterios proporcionados.',404);
        }

        return $this->singUrl($response);
    }
    public function getUtility(): array
    {
        return $this->repository->getUtility();
    }

    public function delete(int $id, UserRequesting $user): void
    {
        $documentsAuth = $this->repository->getCommand(GetAuthsDocumentsDto::fromId($id));
        if(empty($documentsAuth)) {
            throw new NotFoundException('Documento no encontrado.',404);
        }

        $documentCommand = $documentsAuth[0];
        $msmAudit = $documentCommand->messaggeAuditDelete($user);
        $this->repository->delete($id);
        $this->storage->destroy($documentCommand->getKeyDocument());
        $this->dispatchToQueue(
            $msmAudit,
            $user
        );
        
    }
    /**
     * @param AuthsDocumentsViewModel[] $documents
      * @return AuthsDocumentsViewModel[]
      * @throws \Exception
     */
    private function singUrl(array $documents): array
    {
        return array_map(
            function (AuthsDocumentsViewModel $item) {
                $path = $item->getDocumentPath();
                $url =$this->storage->signedUrl($path);
                return $item->WithUrlSigned($url);
            },
            $documents
        );
    }
}