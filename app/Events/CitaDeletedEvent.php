<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CitaDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $id;
    private array $metaData;

    public function __construct(int $id, array $metaData = [])
    {
        $this->id = $id;
        $this->metaData = $metaData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('citas'), // Puedes ajustar el nombre del canal según tu lógica
        ];
    }

    // Getter y Setter para ID
    public function getId(): int
    {
        return $this->id;
    }
    // Getter y Setter para MetaData
    public function getMetaData(): array
    {
        return $this->metaData;
    }


}
