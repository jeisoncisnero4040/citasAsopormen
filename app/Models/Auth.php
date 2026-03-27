<?php

namespace App\Models;

class Auth {
    private string $n_autoriza;
    private string $tiempo;
    private string $procedim;
    private int $cantidad;
    private bool $cerrado;
    private string $especialidad;
    private string $f_vence;
    private string $f_inicial;
    private string $observa;
    private string $convenio;
    private string $cod_entidad;
    private int $total_programadas;
    private int $disponibles;
    private string $entidad;
    private bool $nueva;
    private array $especialidades = [];
    private bool $isInvoiced;
    private string $codeClient;

    public function __construct(
        string $n_autoriza,
        string $tiempo,
        string $procedim,
        int $cantidad,
        bool $cerrado,
        string $especialidad,
        string $f_vence,
        string $f_inicial,
        string $observa,
        string $convenio,
        string $cod_entidad,
        int $total_programadas,
        int $disponibles,
        string $entidad,
        bool $nueva,
        bool $isInvoiced,
        string $codeClient,
    ){
        $this->n_autoriza = $n_autoriza;
        $this->tiempo = $tiempo;
        $this->procedim = $procedim;
        $this->cantidad = $cantidad;
        $this->cerrado = $cerrado;
        $this->especialidad = $especialidad;
        $this->f_vence = $f_vence;
        $this->f_inicial = $f_inicial;
        $this->observa = $observa;
        $this->convenio = $convenio;
        $this->cod_entidad = $cod_entidad;
        $this->total_programadas = $total_programadas;
        $this->disponibles = $disponibles;
        $this->entidad = $entidad;
        $this->nueva = $nueva;
        $this->isInvoiced= $isInvoiced;
        $this->codeClient=$codeClient;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['n_autoriza'],
            $array['tiempo'],
            $array['procedim'],
            (int)$array['cantidad'],
            (bool)$array['cerrado'],
            $array['especialidad'] ?? 'No Encontrada',
            $array['f_vence'],
            $array['f_inicial'],
            $array['observa'] ?? '',
            $array['convenio'],
            $array['cod_entidad'],
            (int)$array['total_programadas'],
            (int)$array['disponibles'],
            $array['entidad'],
            (bool)$array['nueva'],
            (bool)$array['facturada'],
            $array['historia']
        );
    }

    public function toArray(): array
    {
        return [
            'n_autoriza' => $this->n_autoriza,
            'tiempo' => $this->tiempo,
            'procedim' => $this->procedim,
            'cantidad' => $this->cantidad,
            'cerrado' => $this->cerrado,
            'especialidad' => $this->especialidad,
            'f_vence' => $this->f_vence,
            'f_inicial' => $this->f_inicial,
            'observa' => $this->observa,
            'convenio' => $this->convenio,
            'cod_entidad' => $this->cod_entidad,
            'total_programadas' => $this->total_programadas,
            'disponibles' => $this->disponibles,
            'entidad' => $this->entidad,
            'nueva' => $this->nueva,
            'especialidades'=>$this->especialidades,
            'facturada'=>$this->isInvoiced
        ];
    }
    public function getAutoriza():string{
        return $this->n_autoriza;
    }
    public function getSpecialty():string{
        return $this->especialidad;
    }
    public function setSpecialties(array $new):void{
        $this->especialidades = $new;
    }
    public function getCodeClient():string{
        return $this->codeClient;
    }

    
}