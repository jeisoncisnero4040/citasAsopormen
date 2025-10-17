<?php

namespace App\Interfaces;

use App\Models\ProcediproModel;

interface ProcediproRepositoryInterface{
    public function getProcediproByCod(int $id):ProcediproModel;
}