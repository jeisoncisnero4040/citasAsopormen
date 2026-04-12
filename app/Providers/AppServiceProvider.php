<?php

namespace App\Providers;

use App\Services\Prometheus\PrometheusService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton( PrometheusService::class, PrometheusService::class );
        $this->app->singleton(\App\Strategies\PromptFactoryRegistry::class, function ($app) {
            return new \App\Strategies\PromptFactoryRegistry([
                $app->make(\App\Strategies\NewPromptStrategy::class),
                $app->make(\App\Strategies\VerificatedPromptStrategy::class),
                $app->make(\App\Strategies\CancelingApposPromptStrategy::class),
            ]);
        });
        $this->app->bind(\App\Ports\ChatRepositoryPort::class, \App\Repositories\ChatRepository::class);
        $this->app->bind(\App\Kafka\Ports\QueuePort::class, \App\Services\QueueService::class);
        $this->app->bind(\App\Ports\IaPort::class, \App\Services\IaService::class);
        $this->app->bind(\App\Ports\ChannelMessagingPort::class, \App\Services\MessagingService::class);
        $this->app->bind(\App\Ports\ClientsPort::class, \App\Services\FakeUsaerService::class); 
        $this->app->bind(\App\Ports\AppoinmentPort::class, \App\Services\FakeAppointmentsService::class);
        $this->app->bind(\App\Ports\FakeApiCitasPort::class, \App\Services\ApiCitasAsopormenService::class);
        $this->app->bind(\App\Ports\CitasPort::class, \App\Services\CitasService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
