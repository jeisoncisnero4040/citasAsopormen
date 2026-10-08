<?php

namespace App\Dtos;

use App\Dtos\TypeDocumentsAuthSplitDto;

class CreateMassiveAuthsDocumentsDto
{   
    public function __construct(
        private string $clientCode,
        private string $n_autoriza,
        private string $cedula_usuario,
        private string $codigo_eps,
        /**
         * @var TypeDocumentsAuthSplitDto[]
         */
        private array $typeDocumentsAuthSplitDto


    ) {}

    public static function fromArray(array $data): self
    {
        $config = json_decode($data['config'], true);

        return new self(
            $data['clientCode'],
            $data['n_autoriza'],
            $data['cedula_usuario'],
            $data['codigo_eps'],
            array_map(
                fn($item) => TypeDocumentsAuthSplitDto::fromArray($item),
                $config
            )
        );
    }
    public function getNAutoriza(): string{return $this->n_autoriza;}
    /**
     * @return TypeDocumentsAuthSplitDto[]
     */
    public function getTypeDocumentsAuthSplitDto(): array{return $this->typeDocumentsAuthSplitDto;}
    public function getCedulaUser(): string{return $this->cedula_usuario;}
    public function getEpsCode(): string{return $this->codigo_eps;}
    public function getClientCode(): string{return $this->clientCode;}

}