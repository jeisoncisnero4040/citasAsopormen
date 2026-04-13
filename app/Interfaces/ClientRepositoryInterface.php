<?php

namespace App\Interfaces;

use App\Commands\ClientCommand;
use App\Dtos\UpdateUserDto;
use App\Models\ClientView;


interface ClientRepositoryInterface{
    public function updateClient(string $codigo,UpdateUserDto $dto):int;
    public function getPwdByCodigo(string $codigo):array;
    public function setPws(string $codigo,string $newPassword):int;
    public function search(array $filters):array;
    public function create(ClientCommand $client):int;
    public function update(ClientCommand $client):int;
    public function getLastHistory():array;

    /**
     * @return array<ClientView>
     */
    public function get(array $filters = []): array;
    public function toggleActive(string $code,bool $active):array;

    public function getUtility():array;
}