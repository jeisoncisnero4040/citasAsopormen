<?php

namespace App\Ports;

use App\Domain\Chat;

interface ChatRepositoryPort
{
    public function save(Chat $chat): Chat;

    public function findById(string $id): ?Chat;

    public function findByTelephone(string $telephoneNumber): ?Chat;

    public function findAll(int $limit = 50, int $offset = 0): array;

    public function delete(string $id): void;

    public function dropAll(): void;


}