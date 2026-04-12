<?php
namespace App\Services;
use App\Kafka\Ports\QueuePort;
use App\Kafka\Domain\MessageQueue;
use App\Kafka\KafkaProducer;

class QueueService implements QueuePort
{
    private KafkaProducer $producer;

    public function __construct(KafkaProducer $producer)
    {
        $this->producer = $producer;
    }

    public function publish(MessageQueue $message): void
    {
        $this->producer->publish($message);
    }
}