<?php

namespace App\Kafka\Ports;

interface ConsumerQueuePort
{
    public function subscribe(array $topics): void;
    public function consume(): void;
}