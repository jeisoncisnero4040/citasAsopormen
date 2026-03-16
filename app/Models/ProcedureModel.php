<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use stdClass;

class ProcedureModel 
{
    private string $nombre;
    private bool $contable;
    public function __construct(stdClass $procedipro)
    {
        $this->nombre= $procedipro->nombre;
        $this->contable= $procedipro->sumable =='1';
    }
    public function isContable():bool{
        return $this->contable;
    }
}