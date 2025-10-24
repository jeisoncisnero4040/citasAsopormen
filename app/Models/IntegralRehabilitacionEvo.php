<?php

namespace App\Models;
use stdClass;


class IntegralRehabilitacionEvo extends NeuroEvoModel
{
    public string $date;
    public string $finishHour;
    public string $secuencie;
    public string $history;

    public string $target;
    public string $description;
    public string $result;
    public int $id;
    public ?string $procedipro;
    public string $formatPrint;
    public string $profesional;
    public string $tituloUni;
    public string $nroCardPro;

    public ?string $signatureUrl;

    function __construct(stdClass $evo)
    {
        $this->date        = $evo->fecha_registro;
        $this->finishHour  = $evo->hora_ter;
        $this->secuencie   = $evo->sesiones;
        $this->history     = $evo->historia;

        $this->target      = $evo->objetivos;
        $this->description = $evo->descripcion;
        $this->result      = $evo->resultados;
        $this->id          = (int) $evo->id;
        $this->procedipro  = $evo->procedipro;
        $this->companion   = $evo->acompaniante;
        $this->kindred     = $evo->parentezco;
        $this->formatPrint = $evo->formato;
        $this->signaturePath=$evo->firma;

        $this->profesional = $evo->enombre;
        $this->tituloUni   = $evo->titulouni;
        $this->nroCardPro  = $evo->tarjetap;
        $this->cedula      = $evo->cedula;



    }
}