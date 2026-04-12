<?php

namespace App\Strategies;
use App\Contracts\PromptFactoryStrategy;
use App\Domain\Chat;
use App\Domain\Prompt;
use App\Domain\PromptBuilder;
use App\Domain\InputPrompt;
use App\Enums\ChatStatusEnum;
use App\Config\Prompt as PromptConfig;


class VerificatedPromptStrategy implements PromptFactoryStrategy
{
    public function supports(Chat $chat): bool
    {
        return $chat->getStatus() === ChatStatusEnum::IDENTIFIED;
    }

    public function build(Chat $chat): Prompt
    {
        $input = new InputPrompt(stream:PromptConfig::IDENTIFIED, context:$chat->toContextString());
        return PromptBuilder::fromChat($chat)
            ->withModel('gpt-5-nano')
            ->withRole('system')
            ->withStream(PromptConfig::IDENTIFIED)
            ->build();
    }
}