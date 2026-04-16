<?php

namespace App\Commands;
use App\Models\UserRequesting;

class AuthsDocumentsCommand {
    const ROOT_DIRECTORY_STORAGE = 'autorizaciones';
    private ?int $id;
    private string $n_autoriza;
    private int $documentTypeId;
    private string $cedulaUser;
    private string $epsCode;
    private string $created_At;
    private string $clientCode;
    private ?string $nameDocument;
    private ?string $keyDocument; 
    public function __construct(
        string $n_autoriza,
        int $documentTypeId,
        string $cedulaUser,
        string $epsCode,
        string $created_At,
        string $clientCode,
        ?int $id=null,
        ?string $nameDocument=null,
        ?string $keyDocument=null
    ) {
        $this->id = $id;
        $this->clientCode = $clientCode;
        $this->n_autoriza = $n_autoriza;
        $this->documentTypeId = $documentTypeId;
        $this->cedulaUser = $cedulaUser;
        $this->epsCode = $epsCode;
        $this->created_At = $created_At;
        $this->keyDocument = $keyDocument;
        $this->nameDocument = $nameDocument;

    }
    public function buildkeyDocument(): string
    {
        return $this->clientCode . '/'. self::ROOT_DIRECTORY_STORAGE . '/' . $this->n_autoriza;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNAutoriza(): string
    {
        return $this->n_autoriza;
    }
    public function getDocumentTypeId(): int
    {
        return $this->documentTypeId;  
    }
    public function getCedulaUser(): string
    {
        return $this->cedulaUser;
    }
    public function getEpsCode(): string
    {
        return $this->epsCode;
    }
    public function getCreatedAt(): string
    {
        return $this->created_At;
    }
    public function getNameDocument(): ?string
    {
        return $this->nameDocument;
    }
    public function getClientCode(): string
    {
        return $this->clientCode;
    }
    public function getKeyDocument(): ?string

    {
        return $this->keyDocument;
    }
    public function setNameDocument(string $name): void
    {
        $this->nameDocument = $name;
    }
    public function messaggeAudit(UserRequesting $user):string{
        return "El usuario ". $user->getUsername().  "añadio el documento ". $this->nameDocument. 
        " a la autorizacion  ". $this->n_autoriza ." el dia ". $this->created_At;
    }
    public function messaggeAuditDelete(UserRequesting $user):string{
        return "El usuario ". $user->getUsername().  "elimino el documento ". $this->nameDocument. 
        " de la autorizacion  ". $this->n_autoriza ." el dia ". now()->toDateTimeString();
    }
    public function setKeyDocument(string $key): void
    {
        $this->keyDocument = $key;
    }

}