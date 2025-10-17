<?php

namespace App\Interfaces;

use App\Dtos\UpdateProDto;
use App\Models\ProfesionalModel;

interface ProfesionalRepositoryInterface{
    public function  getProfesionalByIdentity(string $identityNumber):ProfesionalModel;
    public function  changePasswordProfesional(string $cedula,string $newPassword,bool $firstChange):int;
    public function  getProcedipros(string $cedula):array;
    public function  searchByString(string $param):array;
    public function  updatePro(string $cedula,UpdateProDto $dto):void;

}