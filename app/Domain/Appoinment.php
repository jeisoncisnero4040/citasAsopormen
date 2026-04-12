<?php

namespace App\Domain;

class Appoinment
{
    private string $ids;
    private string $date;
    private string $nameDoctor;
    private string $time;
    private string $status;
    private ?string $razonCancelacion=null;

    public function __construct(string $ids, string $date, 
    string $nameDoctor, string $time, string $status, ?string $razonCancelacion = null)
    {
        $this->ids = $ids;
        $this->date = $date;
        $this->nameDoctor = $nameDoctor;
        $this->time = $time;
        $this->status = $status;
        $this->razonCancelacion = $razonCancelacion;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            ids: $data['ids'] ?? '',
            date: $data['date'] ?? '',
            nameDoctor: $data['nameDoctor'] ?? '',
            time: $data['time'] ?? '',
            status: $data['status'] ?? '',
            razonCancelacion: $data['razonCancelacion'] ?? null
        );
        
    }
    public function getIds(): string
    {
        return $this->ids;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getNameDoctor(): string
    {        return $this->nameDoctor;
    }
    public function getTime(): string
    {        return $this->time;
    }
    public function getStatus(): string
    {        return $this->status;  
    }
    public function getRazonCancelacion(): ?string
    {
        return $this->razonCancelacion;
    }
    public function setRazonCancelacion(?string $razonCancelacion): void
    {
        $this->razonCancelacion = $razonCancelacion;
    }
    public function toArray(): array
    {
        return [
            'ids' => $this->ids,
            'date' => $this->date,
            'nameDoctor' => $this->nameDoctor,
            'time' => $this->time,
            'status' => $this->status,
            'razonCancelacion' => $this->razonCancelacion
        ];
    }
}