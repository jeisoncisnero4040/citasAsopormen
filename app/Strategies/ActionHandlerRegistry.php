<?php

namespace App\Strategies;

use App\Contracts\ActionHandlerStrategy;
use App\Domain\IaResponse;

class ActionHandlerRegistry
{
    /** @var ActionHandlerStrategy[] */
    private array $strategies;

    /**
     * @param ActionHandlerStrategy[] $strategies
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    public function getHandlerForResponse(IaResponse $iaResponse): ?ActionHandlerStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($iaResponse)) {
                return $strategy;
            }
        }

        return null;
    }
}