<?php

namespace App\Contracts;
use App\Domain\Chat;
use App\Enums\ChatStatusEnum;

interface ChatStatusUpdatable
{
    public function setStatus(Chat $chat, ChatStatusEnum $status): Chat;
}