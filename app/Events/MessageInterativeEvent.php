<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageInterativeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private mixed $data;
    private float $currentStatus;
    private float $newStatus;
    private string $numCel;

    /**
     * Create a new event instance.
     */
    public function __construct(mixed $data, float $currentStatus,float $newStatus,string $numCel)
    {   
        $this->data=$data;
        $this->currentStatus=$currentStatus;
        $this ->newStatus=$newStatus;
        $this->numCel=$numCel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('message-interative'),
        ];
    }
    public function getData(): mixed
    {
        return $this->data;
    }

    public function getCurrentStatus(): float
    {
        return $this->currentStatus;
    }

    public function getNumCel(): string
    {
        return $this->numCel;
    }
    public function getNextStatus():float
    {
        return $this->newStatus;
    }
}
