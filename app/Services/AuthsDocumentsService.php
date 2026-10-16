<?php

namespace App\Services;

use App\Services\QueueService;
use App\Dtos\CreateAuthsDocumentsDto;
use App\Dtos\GetAuthsDocumentsDto;
use App\Interfaces\AuthsDocumentsPort;
use App\Commands\AuthsDocumentsCommand;
use App\Domain\Code;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Exceptions\CustomExceptions\NotFoundException;
use App\Models\UserRequesting;
use Illuminate\Http\UploadedFile;
use App\Models\AuthsDocumentsViewModel;
use App\Dtos\CreateMassiveAuthsDocumentsDto;
use App\Dtos\TypeDocumentsAuthSplitDto;
use App\Files\Domain\Pdf;
use App\Files\Domain\SubRangeIndex;
use App\Files\Ports\PdfJoinerPort;
use App\Domain\DocumentKeyStorage;
use App\Interfaces\StorageInterface;
use App\Domain\DocumentClient;
use App\Services\DocumentClientService;

class AuthsDocumentsService extends BaseService
{
    private AuthsDocumentsPort $repository;
    private StorageInterface $storage;
    private PdfJoinerPort $pdfJoiner;
    protected QueueService $queueService;
    private DocumentClientService $documentClientService;

    public function __construct(
        AuthsDocumentsPort $repository,
        QueueService $queueService,
        PdfJoinerPort $pdfJoiner,
        StorageInterface $storage,
        DocumentClientService $documentClientService
    ) {
        parent::__construct($queueService);
        $this->repository = $repository;
        $this->queueService = $queueService;
        $this->pdfJoiner = $pdfJoiner;
        $this->storage = $storage;
        $this->documentClientService = $documentClientService;
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
            created_At: now()->format('Y-d-m H:i:s'),
            clientCode: $dto->getClientCode()

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
     * @param CreateMassiveAuthsDocumentsDto $dto
     * @return AuthsDocumentsViewModel[]
     */
    public function createFromOnlyDocument(CreateMassiveAuthsDocumentsDto $dto, Pdf $file): array
    {
        $docuemntsCollection = [];
        $updatedClientDocument = false;

        $config = $dto->getTypeDocumentsAuthSplitDto();
        foreach ($config as  $item) {
            $subRangeIndex = new SubRangeIndex($item->getStartIndex(), $item->getEndIndex());
            $pdf= $this->pdfJoiner->splitBySubRange($file, $subRangeIndex);
            if($item->isDocumentUser()) {
                $this->updateClientDocument($pdf, new Code($dto->getClientCode()));
                $updatedClientDocument = true;
                continue;
            }
            $keyStorage = $this->loadSplitPdf($dto, $pdf);
            $docuemntsCollection[] = $this->buildCommand($dto, $item, $keyStorage);
        }

        if ($updatedClientDocument && empty($docuemntsCollection)) {
            return [];
        }

        $this->repository->createMany($docuemntsCollection);
        $dtoGet = new GetAuthsDocumentsDto(
            n_autoriza: $dto->getNAutoriza(),
            clientCode: $dto->getClientCode()
        );
        return $this->get($dtoGet);
        

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
    public function loadSplitPdf(CreateMassiveAuthsDocumentsDto $dto,Pdf $file): DocumentKeyStorage
    {
        $keyStorage = new DocumentKeyStorage(
            clientCode: $dto->getClientCode(),
            n_autoriza: $dto->getNAutoriza()
        );
        $this->storage->put($keyStorage, $file->getContent());
        return $keyStorage;
    }
    private function buildCommand(CreateMassiveAuthsDocumentsDto $dto, TypeDocumentsAuthSplitDto $item, DocumentKeyStorage $keyStorage): AuthsDocumentsCommand
    {
        return new AuthsDocumentsCommand(
            n_autoriza: $dto->getNAutoriza(),
            documentTypeId: $item->getTypeDocumentId(),
            cedulaUser: $dto->getCedulaUser(),
            epsCode: $dto->getEpsCode(),
            created_At: now()->format('Y-d-m H:i:s'),
            clientCode: $dto->getClientCode(),
            keyDocument: $keyStorage->getKey()
        );
    }
    private function updateClientDocument(Pdf $file, Code $clientCode): void
    {
        $documentClient = new DocumentClient(
            codeClient: $clientCode,
            pdf: $file
        );
        $this->documentClientService->update($documentClient);
    }
}