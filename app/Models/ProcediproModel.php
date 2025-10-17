<?php

namespace App\Models;
use stdClass;

class ProcediproModel{
    private int $code;
    private string $nombre;
    private string $appoType;
    private int $evoTipo;
    private string $printFormat;

    public function  __construct(stdClass $procedipro){
        $this->code=$procedipro->id;
        $this->nombre=$procedipro->nombre;
        $this->appoType=$procedipro->tipo_cita;
        $this->evoTipo=(int)$procedipro->tipo_registro;
        $this->printFormat=$procedipro->formatoimpevol;
    }

    public function getCode():int{return $this->code;}
    public function getNombre():string{return $this->nombre;}
    public function getTypeAppo():string {return $this->appoType;}
    public function getTypeEvo():string {return $this->evoTipo;}

    public function formatPrintOfPsico(){
        return $this->printFormat=='formatoevol_neuropsi';
    }
    public function formatPrintOfAba():bool{
        return $this->printFormat=='formatoevol_aba';
    }
    public function formatPrintOfRehabilitacion():bool{
        return $this->printFormat=='formatoevol_reinte';
    }


    public function isPelvic():bool{
        return false;
    }

    public function isHidric():bool{
        return false;
    }
    public function isFisio():bool{
        return false;
    }
}