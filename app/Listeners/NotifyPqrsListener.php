<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\EmailService;
use App\Events\NotifyPqrsEvent;

class NotifyPqrsListener
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService=$emailService;
    }

    /**
     * Handle the event.
     */
    public function handle(NotifyPqrsEvent $event): void
    {
        $dataPqrs= $event->getDataPqrs();
        $this->emailService->notifyPqrs($dataPqrs);
    }
}
