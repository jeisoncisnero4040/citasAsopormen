<?php

namespace App\Models;
use stdClass;

class DisponilityEvoModel {
    private int $id;
    private int $asistidas;
    private int $cantidad;
    private int $disponibles;
    private string $table;

    public function __construct(stdClass $info)
    {
        $this->asistidas   = (int)$info->asistidas;  
        $this->cantidad    = (int)$info->cantidad;
        $this->table       = $info->tabla;
        $this->disponibles = (int)$info->disponibles;
        $this->id          = (int) $info->id;
    }

    public function isAvaible(): bool
    {
        return $this->disponibles > 0;
    }

    // --- Getters ---
    public function getAsistidas(): int
    {
        return $this->asistidas;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function getDisponibles(): int
    {
        return $this->disponibles;
    }

    public function getTable(): string
    {
        return $this->table;
    }
    public function getId():int{
        return $this->id;
    }

    // --- Setters ---
    public function setAsistidas(int $asistidas): void
    {
        $this->asistidas = $asistidas;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function setDisponibles(int $disponibles): void
    {
        $this->disponibles = $disponibles;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }
    public function toArray():array{
        return [
            'asistidas'=>$this->asistidas,
            'cantidad'=>$this->cantidad,
            'disponibles'=>$this->disponibles
        ];
    }
}
