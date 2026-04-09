<?php

namespace App\Dtos;

final class ExternalProcedureDto
{
    private string $code;
    private int $quantity;

    public function __construct(string $code, int $quantity)
    {
        $this->code = $code;
        $this->quantity = $quantity;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['cupCode'] ?? '',
            quantity: (int) ($data['ammount'] ?? 0)
        );
    }

    public function getCode(): string { return $this->code; }
    public function getQuantity(): int { return $this->quantity; }
}