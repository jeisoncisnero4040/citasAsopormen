<?php

namespace App\Strategies;

use App\Contracts\PromptFactoryStrategy;
use App\Domain\Chat;
use App\Domain\Prompt;
use App\Domain\PromptBuilder;
use App\Enums\ChatStatusEnum;
use App\Config\Prompt as PromptConfig;

class NewPromptStrategy implements PromptFactoryStrategy
{
    public function supports(Chat $chat): bool
    {
        return $chat->getStatus() === ChatStatusEnum::NEW;
    }

    public function build(Chat $chat): Prompt
    {

        return PromptBuilder::fromChat($chat)
            ->withModel('gpt-5-nano')
            ->withRole('system')
            ->withStream(PromptConfig::NEW)
            ->build();
    }
}