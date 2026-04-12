<?php

namespace App\Domain;


class ExternalResponse
{
    public function __construct(
        private readonly ?bool $success,
        private readonly ?string $message,
        private readonly ?array $data,
    ) {}

    public function success(): ?bool
    {
        return $this->success;
    }
    public static function fromSuccess(?string $message="success", ?array $data=null): self
    {
        return new self(true, $message, $data);
    }
    public static function fromError(string $message, ?array $data=[]): self
    {
        return new self(false, $message, $data);
    }


    public function message(): ?string
    {
        return $this->message;
    }

    public function data(): ?array
    {
        return $this->data;
    }

}