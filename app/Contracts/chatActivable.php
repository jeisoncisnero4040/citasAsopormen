<?php

namespace App\Contracts;

use App\Domain\Chat;

interface chatActivable
{
    public function  active(Chat $chat): void;
    
}