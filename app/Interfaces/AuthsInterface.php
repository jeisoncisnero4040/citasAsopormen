<?php

namespace App\Interfaces;

interface AuthsInterface{
    public function getProfesionalsAuths(string $profesionalCed):array;
    public function closeAuth(string $razon,int $idAutoriz,string $profesional,array $ids):void;
    public function getAuthsByIds(array $ids):array;
}