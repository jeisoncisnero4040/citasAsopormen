<?php

namespace App\Interfaces;

use App\Dtos\GetUsersDto;

interface UserPort {
    public function get(GetUsersDto $dto): array;
}