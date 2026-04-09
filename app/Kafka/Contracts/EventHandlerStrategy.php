<?php

namespace App\Kafka\Contracts;

interface EventHandlerStrategy
{
    public function supports(string $event): bool;

    public function handle(array $payload): void;
}