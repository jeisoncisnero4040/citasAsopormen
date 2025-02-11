<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitasModel extends Model
{
    use HasFactory;
    protected $table='citas_waiting';
    protected $fillable=['number','cita_ids','canceled','accepted','waiting','date_cita','observations'];

}
