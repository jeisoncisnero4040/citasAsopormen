<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NotifyPqrsEvent;
use App\Listeners\NotifyPqrsListener;
use App\Listeners\RegisterLogPqrsSendedAreaListener;
use App\Events\RegisterLogPqrsSendedAreaEvent;



class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        NotifyPqrsEvent::class => [
            NotifyPqrsListener::class,
        ],
        RegisterLogPqrsSendedAreaEvent::class => [
            RegisterLogPqrsSendedAreaListener::class,
        ],
        
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
