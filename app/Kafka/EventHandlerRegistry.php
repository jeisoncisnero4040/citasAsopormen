<?php

namespace App\Kafka;

use App\Kafka\Contracts\EventHandlerStrategy;

class EventHandlerRegistry
{
    /**
     * @var EventHandlerStrategy[]
     */
    private array $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function resolve(string $event): ?EventHandlerStrategy
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($event)) {
                return $handler;
            }
        }

        return null;
    }
}