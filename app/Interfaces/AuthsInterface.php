<?php


namespace App\Interfaces;

use App\Dtos\GetAuthsDto;
use App\Models\Auth;
interface AuthsInterface{
    /**
     * @return array<Auth>
     */
    public function get(GetAuthsDto $dto):array;

    public function getDetailAuth(GetAuthsDto $dto):array;

    public function getDetailApposAppos(GetAuthsDto $dto):array;

    public function getDetailsOrders(GetAuthsDto $dto):array;
}


    