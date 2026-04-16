<?php

namespace App\Models;

class AuthsDocumentsViewModel{
    private ?int $id;
    private string $documentName;
    private string $n_autoriza;
    private string $clientCode;
    private string $clientCedula;
    private string $epsCode;
    private string $createdAt;
    private string $documentPath;
    private string $publicUrl = '';
    private int $id_doc;

    public function __construct(
        ?int $id = null,
        string $documentName = '',
        string $n_autoriza = '',
        string $clientCode = '',
        string $clientCedula = '',
        string $epsCode = '',
        string $createdAt = '',
        string $documentPath = '',
        int $id_doc = 0

    ) {
        $this->id = $id;
        $this->documentName = $documentName;
        $this->n_autoriza = $n_autoriza;
        $this->clientCode = $clientCode;
        $this->clientCedula = $clientCedula;
        $this->epsCode = $epsCode;
        $this->createdAt = $createdAt;
        $this->documentPath = $documentPath;
        $this->id_doc = $id_doc;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDocumentName(): string
    {
        return $this->documentName; 
    }
    public function getNAutoriza(): string
    {
        return $this->n_autoriza; 
    }
    public function getClientCode(): string
    {
        return $this->clientCode;
    }
    public function getClientCedula(): string
    {
        return $this->clientCedula;
    }
    public function getEpsCode(): string
    {
        return $this->epsCode;  
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
    public function getDocumentPath(): string
    {        return $this->documentPath;
    }
    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }
    public function WithUrlSigned(string $url): self
    {
        $this->publicUrl = $url;
        return $this;
    }
    public function getIdDoc(): int
    {
        return $this->id_doc;
    }   
 
}