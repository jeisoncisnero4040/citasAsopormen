<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;  
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $auditMessage;

    public function __construct(string $auditMessage)
    {
        $this->auditMessage = $auditMessage;
    }

    public function getAuditMessage(): string
    {
        return $this->auditMessage;
    }

     
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('audits'),  
        ];
    }
}
