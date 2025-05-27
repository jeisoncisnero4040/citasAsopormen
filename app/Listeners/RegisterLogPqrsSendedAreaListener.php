<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use App\Events\RegisterLogPqrsSendedAreaEvent;

class RegisterLogPqrsSendedAreaListener
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(RegisterLogPqrsSendedAreaEvent $event): void
    {
        $data = $event->getDataPqrs();
        $token= $event->getToken();
        $this->auditService->sendNewRegister($data,$token);

    }
}
