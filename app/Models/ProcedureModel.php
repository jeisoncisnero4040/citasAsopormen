<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureModel extends Model
{
    use HasFactory;

 
    protected $table = 'procedipro';
     
 
    protected $fillable = [
        'id',
        'nombre',  
    ];
}