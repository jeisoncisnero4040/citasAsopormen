<?php

namespace App\Contracts;


use App\Domain\Chat;
use App\Domain\Prompt;

interface PromptFactoryStrategy
{
    public function supports(Chat $chat): bool;

    public function build(Chat $chat): Prompt;
}