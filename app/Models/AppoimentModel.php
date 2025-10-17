<?php

namespace App\Models;

use stdClass;

class AppoimentModel{
    private string $hora;
    private string $cliente;
    private int $edad;
    private string $procedimiento ;
    private string $autorizacion;
    private bool $asistio;
    private bool $cancelada;
    private bool $no_asistida;
    private string $razon_cancelamiento;
    private string $fin;
    private bool $evolucionable;
    private bool $actual;

    public function __construct(stdClass $appoiment)
    {
        $this->hora = $appoiment->hora;
        $this->cliente = $appoiment->nombre;
        $this->edad = (int) $appoiment->edad;
        $this->procedimiento = $appoiment->procedimiento;
        $this->autorizacion = $appoiment->autoriz;

        $this->asistio = $appoiment->asistio == 1;
        $this->cancelada = $appoiment->cancelada == 1;
        $this->no_asistida = $appoiment->na == 1;

        $this->razon_cancelamiento = $appoiment->realizar;
        $this->fin = $appoiment->fin;
        $this->evolucionable = $appoiment->evolucionable == 1;
        $this->actual = $appoiment->actual == 1;
    }
    
    public function getHora(): string { return $this->hora; }
    public function getCliente(): string { return $this->cliente; }
    public function getEdad(): int { return $this->edad; }
    public function getProcedimiento(): string { return $this->procedimiento; }
    public function getAutorizacion(): string { return $this->autorizacion; }
    public function isAsistio(): bool { return $this->asistio; }
    public function isCancelada(): bool { return $this->cancelada; }
    public function isNoAsistida(): bool { return $this->no_asistida; }
    public function getRazonCancelamiento(): string { return $this->razon_cancelamiento; }
    public function getFin(): string { return $this->fin; }
    public function isEvolucionable(): bool { return $this->evolucionable; }
    public function isActua(): bool { return $this->actual; }

    public function toArray(): array
        {
            return [
                'hora' => $this->hora,
                'cliente' => $this->cliente,
                'edad' => $this->edad,
                'procedimiento' => $this->procedimiento,
                'autorizacion' => $this->autorizacion,
                'asistio' => $this->asistio,
                'cancelada' => $this->cancelada,
                'no_asistida' => $this->no_asistida,
                'razon_cancelamiento' => $this->razon_cancelamiento,
                'fin' => $this->fin,
                'evolucionable' => $this->evolucionable,
                'actua' => $this->actual,
            ];
        }




}