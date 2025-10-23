<?php

namespace App\Dtos;

use App\Exceptions\CustomExceptions\BadRequestException;
use Illuminate\Http\Request;

class SetAutorizAppoDto
{
    private string $profesional;

    private string $usuario;
    private string $old;
    private string $new;


    public function __construct(Request $request)
    {
        $this->profesional = $request->input('profesional') ?? '';
        $this->usuario = $request->input('client') ?? '';
        $this->old = $request->input('current_auth') ?? '';
        $this->new = $request->input('new_auth') ?? '';
        $this->validate();
    }

    // Getters
    public function getProfesional(): string { return $this->profesional; }
    public function getClient(): string { return $this->usuario; }
    public function getOld(): string { return $this->old; }
    public function getNew(): string { return $this->new; }

    // Validación con mensajes claros
    public function validate(): void
    {
        if (empty($this->profesional)) {
            throw new BadRequestException("Debe indicar el nombre del profesional que autoriza la cita.", 400);
        }
        if (empty($this->usuario)) {
            throw new BadRequestException("Debe especificar el usuario al que pertenece la cita.", 400);
        }
        if (empty($this->old)) {
            throw new BadRequestException("El valor anterior de la autorización no puede estar vacío.", 400);
        }
        if (empty($this->new)) {
            throw new BadRequestException("Debe indicar el nuevo valor de la autorización.", 400);
        }

    }

    // Para persistencia
    public function toPersistence(): array
    {
        return [
            'autoriz' => $this->new
        ];
    }
}
