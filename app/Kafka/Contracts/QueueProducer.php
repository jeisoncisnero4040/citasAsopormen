<?php

namespace App\Kafka\Contracts;

use app\Kafka\Domain\MessageQueue;


interface QueueProducer {
    public function publish(MessageQueue $message): void;
}