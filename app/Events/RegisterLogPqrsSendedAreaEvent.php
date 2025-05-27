<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterLogPqrsSendedAreaEvent
{
    use Dispatchable, SerializesModels;

    private array $dataPqrs;
    private string $token;
    public function __construct(array $dataPqrs,string $token)
    {
        $this->dataPqrs=$dataPqrs;
        $this->token=$token;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
    public function getDataPqrs(){
        return $this->dataPqrs;
    }
    public function getToken(){
        return $this->token;
    }
}
