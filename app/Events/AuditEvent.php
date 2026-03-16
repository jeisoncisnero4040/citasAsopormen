<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $auditMessage;
    private string $cedula;

    public function __construct(string $auditMessage,string $cedula)
    {
        $this->auditMessage = $auditMessage;
        $this->cedula=$cedula;
    }

    public function getAuditMessage(): string
    {
        return $this->auditMessage;
    }
    public function getCedula():string{
        return $this->cedula;
    }

     
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('audits'),  
        ];
    }
}
