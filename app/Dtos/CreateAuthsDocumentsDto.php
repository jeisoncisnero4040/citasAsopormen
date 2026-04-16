<?php

namespace App\Dtos;

use GuzzleHttp\Client;

class CreateAuthsDocumentsDto
{   
    private string $n_autoriza;
    private int $tipo_documento_id;
    private string $cedula_usuario;
    private string $codigo_eps;
    private string $clientCode;   
    private string $nameDocument;
    public function __construct(
        string $clientCode,
        string $n_autoriza,
        int $tipo_documento_id,
        string $cedula_usuario,
        string $codigo_eps,
        string $nameDocument
    ) {
        $this->clientCode = $clientCode;
        $this->n_autoriza = $n_autoriza;
        $this->tipo_documento_id = $tipo_documento_id;
        $this->cedula_usuario = $cedula_usuario;
        $this->codigo_eps = $codigo_eps;
        $this->nameDocument = $nameDocument;

    }

    public static function fromArray(array $data): self
    {
        return new self(
                $data['clientCode'],
                $data['n_autoriza'],
                $data['tipo_documento_id'],
                $data['cedula_usuario'],
                $data['codigo_eps'],
                $data['nameDocument']
                );
        }
    public function getNAutoriza(): string{return $this->n_autoriza;}
    public function getDocumentTypeId(): int{return $this->tipo_documento_id;}
    public function getCedulaUser(): string{return $this->cedula_usuario;}
    public function getEpsCode(): string{return $this->codigo_eps;}
    public function getClientCode(): string{return $this->clientCode;}
    public function getNameDocument(): string{return $this->nameDocument;}
}