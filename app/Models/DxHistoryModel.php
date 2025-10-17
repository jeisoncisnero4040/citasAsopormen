<?php

namespace App\Models;
use stdClass;

class DxHistoryModel
{
    public string $historia;
    public ?string $dxEntrada;
    public ?string $dxSalida;
    public ?string $acompaniante;
    public ?string $parentesco;


    public function __construct(stdClass $row)
    {
        $this->historia     = $row->historia;
        $this->dxEntrada    = $row->dx_entrada ?? null;
        $this->dxSalida     = $row->dx_salida ?? null;
        $this->acompaniante = $row->acompaniante_asp ?? null;
        $this->parentesco   = $row->parentezco_acompaniante_asp ?? null;
    }
    public function toArray(){
        return[
            'entry'=>$this->dxEntrada,
            'out'=>$this->dxSalida,
            'companion'=>$this->acompaniante,
            'kindred'=>$this->parentesco
        ];
    }
}