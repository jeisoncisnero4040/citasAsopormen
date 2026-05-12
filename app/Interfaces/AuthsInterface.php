<?php


namespace App\Interfaces;

use App\Dtos\GetAuthsDto;
use App\Models\Auth;
use App\Commands\AuthCommand;

interface AuthsInterface{
    /**
     * @return array<Auth>
     */
    public function get(GetAuthsDto $dto):array;

    /**
     * @param array<AuthCommand> $auths  
     * @return array<int>
     */
    public function saveMany(array $auths):array;

    public function getByIds(array $ids): array;

    public function getDetailAuth(GetAuthsDto $dto):array;

    public function getDetailApposAppos(GetAuthsDto $dto):array;

    public function getDetailsOrders(GetAuthsDto $dto):array;

    /**
     * @param array<Auth> $auths
     * @return array<int>
     */
    public function delete(array $auths): array;

    /**
     * @return array<AuthCommand>
     */
    public function getCommand(GetAuthsDto $dto): array;

    /**
     * @param array<AuthCommand> $auths
     *
     * @return void
     */
    public function updateMany(array $auths,string $oldCodeAuth): void;

    
}


    