<?php

namespace App\Domain;

final class Prompt
{
    public function __construct(
        private readonly ?string $model,
        private readonly ?string $input,
        private readonly ?string $role = 'user'
    ) {}

    public function model(): ?string
    {
        return $this->model;
    }
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'input' => $this->input,
            'role' => $this->role,
        ];
    }

    public function input(): ?string
    {
        return $this->input;
    }
    public function role(): ?string
    {
        return $this->role;
    }

    public function withModel(?string $model): self
    {
        return new self($model, $this->input, $this->role);
    }

    public function withInput(?string $input): self
    {
        return new self($this->model, $input, $this->role);
    }
    public function withRole(?string $role): self
    {
        return new self($this->model, $this->input, $role);
    }
}