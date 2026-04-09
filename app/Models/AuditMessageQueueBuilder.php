<?php

namespace App\Models;
use App\Kafka\Domain\MessageQueue;

class AuditMessageQueueBuilder
{

    
    private string $topic = 'audit-topic';
    private string $event = 'AUDIT_EVENT';
    private array $data = [];

    public function __construct()
    {
       {}
    }
    public static function create(): self
    {
        return new self();
    }

    public function withData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
    public function build(): MessageQueue
    {
        return MessageQueue::create(
            topic: $this->topic,
            event: $this->event
        )->withData($this->data);
    }

    
}