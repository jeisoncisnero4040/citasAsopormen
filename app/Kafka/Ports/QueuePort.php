<?php

namespace App\Kafka\Ports;

use App\Kafka\Domain\MessageQueue;

interface QueuePort
{
    public function publish(MessageQueue $message): void;
}