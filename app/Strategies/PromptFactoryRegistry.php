<?php

namespace App\Strategies;

use App\Contracts\PromptFactoryStrategy;
use App\Domain\Chat;
use App\Domain\Prompt;

class PromptFactoryRegistry
{
    /** @var PromptFactoryStrategy[] */
    private array $strategies = [];

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function build(Chat $chat): Prompt
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($chat)) {
                return $strategy->build($chat);
            }
        }

        throw new \RuntimeException('No PromptStrategy found for status: ' . $chat->getStatus()->value);
    }
}