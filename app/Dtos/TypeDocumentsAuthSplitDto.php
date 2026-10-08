<?php

namespace App\Dtos;


class TypeDocumentsAuthSplitDto
{
    public function __construct(
        private int $startIndex,
        private int $endIndex,
        private int $typeDocumentId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['initIndex'],
            $data['endIndex'],
            $data['typeDocumentId']
        );
    }
    public function getStartIndex(): int
    {
        return $this->startIndex;
    }
    public function getEndIndex(): int
    {
        return $this->endIndex;
    }
    public function getTypeDocumentId(): int
    {
        return $this->typeDocumentId;
    }
}