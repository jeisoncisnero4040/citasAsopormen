<?php

namespace App\Models;


use stdClass;

class NeuroEvoModel{
    public string $date;
    public string $history;
    public string $cedula;
    public string $description;
    public string $analisys;
    public string $target;
    public int $id;
    public string $observation;
    public string $plan;
    public string $profesional;
    public string $tituloUni;
    protected string $signaturePath;
    public string $nroCardPro;
    public string $formatPrint;
    public ?string $companion;
    public ?string $kindred;
    public ?string $signatureUrl;

    public function __construct(stdClass $evo)
    {
        $this->date=$evo->fecha_registro;
        $this->history=$evo->historia;
        $this->cedula =$evo->idter;
        $this->description=$evo->descripcion;
        $this->analisys=$evo->analisis;
        $this->target=$evo->objetivo;
        $this->id=(int) $evo->id;
        $this->observation=$evo->observacion;
        $this->plan=$evo->plam;
        $this->profesional=$evo->enombre;
        $this->tituloUni=trim($evo->titulouni);
        $this->signaturePath=$evo->firma;
        $this->nroCardPro=trim($evo->tarjetap);
        $this->formatPrint=$evo->formato;
        $this->kindred=$evo->parentezco;
        $this->companion=$evo->acompaniante;



    }
    public function setUrl(?string $url):void{$this->signatureUrl=$url;}
    
    public function getPathUrl(): ?string
    {
        if ($this->signaturePath === null || $this->signaturePath === '') {
            return null; 
        }

        $relativePath = str_replace(
            ['D:\\MANAGER\\FIRMAS\\', 'D:/MANAGER/FIRMAS/'],
            '',
            $this->signaturePath
        );
        return str_replace('\\', '/', $relativePath);
    }
    public function toArray(): array
    {
        $data = get_object_vars($this); 
        unset($data['signaturePath']); 
        return $data;
    }
}