<?php

namespace App\Dtos;
use App\Exceptions\CustomExceptions\BadRequestException;


final class ExternalProcedureDto
{
    private string $code;
    private int $quantity;

    public function __construct(string $code, int $quantity)
    {
        if(empty($code)) {
            throw new BadRequestException('El código del procedimiento es obligatorio',400);
        }
        if ($quantity <= 0) {
            throw new BadRequestException('La cantidad del procedimiento debe ser mayor a 0',400);
        }
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