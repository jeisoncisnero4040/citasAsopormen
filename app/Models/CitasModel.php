<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitasModel extends Model
{
     
    protected $table = 'citas';
     

 
    protected $fillable = [
        'id',
        'nro_hist',  
        'fecha',
        'hora',
        'cedprof',
        'tiempo',
        'procedim',
        'ced_usu',
        'registro',
        'fec_hora',
        'numAutoriza',
        'regobserva',
        'codent',
        'codent2',
        'procedipro',
        'recordatorio_wsp'


    ];
    public $timestamps = false;

}
