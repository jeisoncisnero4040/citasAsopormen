<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\citaCreateEvent::class => [
            \App\Listeners\SaveAuditListener::class,

        ],
        \App\Events\CitaDeletedEvent::class => [
            \App\Listeners\CitaDeletedListener::class,
            
        ],
        \App\Events\citaCanceledEvent::class=>[
            \App\Listeners\citaCanceledListener::class,
        ],
        \App\Events\ChangeProfesionalEvent::class=>[
            \App\Listeners\ChangeProfesionalListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
