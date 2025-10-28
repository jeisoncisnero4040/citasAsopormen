<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndBranchChatEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected $numberCel;
    public function __construct($numberCel)
    {
        $this->numberCel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('end-branch'),
        ];
    }
    public function getNumberCel(){
        return $this->numberCel;
    }
}
