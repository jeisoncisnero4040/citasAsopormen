<?php

namespace App\Contracts;

use App\Domain\IaResponse;
use App\Domain\Chat;

interface ActionHandlerStrategy
{
    public function supports(IaResponse $iaResponse): bool;

    public function handle(IaResponse $iaResponse, Chat $chat): void;
}