<?php

namespace App\Services;

use App\Kafka\Ports\QueuePort;
use App\Kafka\KafkaProducer;
use App\Kafka\Domain\MessageQueue;

class QueueService implements QueuePort{
    private KafkaProducer $producer;
    public function __construct(KafkaProducer $producer) {
        $this->producer = $producer;
    }
    public function publish(MessageQueue $message): void
    {
       $this->producer->publish($message);
    }
}