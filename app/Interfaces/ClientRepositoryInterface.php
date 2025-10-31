<?php

namespace App\Interfaces;

use App\Models\ClientModel;

interface ClientRepositoryInterface{
    public function searchClient(string $params):array;
    public function getClienByCode(string $codigo,string $profesional):ClientModel;
    public function getProceduresClient(string $codigo):array;
    public function getInfoClient(string $codigo):array;
}