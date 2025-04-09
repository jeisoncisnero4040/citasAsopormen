<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class citaCreateEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private array $ids;
    private array $schedule;
    private array $dataCita;

    public function __construct(array $ids, array $schedule, array $dataCita)
    {
        $this->ids = $ids;
        $this->schedule = $schedule;
        $this->dataCita = $dataCita;
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

    // Getters
    public function getIds(): array
    {
        return $this->ids;
    }

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function getDataCita(): array
    {
        return $this->dataCita;
    }
}
