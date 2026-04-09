<?php

namespace App\Providers;

use App\Services\Prometheus\PrometheusService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void{

        $this->app->singleton( PrometheusService::class, PrometheusService::class );
    
        $this->app->bind(
            \App\Interfaces\ClientRepositoryInterface::class,
            \App\Repositories\ClientRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\AuditInterface::class,
            \App\Repositories\FakeAuditRepository::class
        );
        $this->app->bind(
            \App\Interfaces\StorageInterface::class,
            \App\Services\StorageService::class
        );
        $this->app->bind(
            \App\Interfaces\AuthsInterface::class,
            \App\Repositories\AuthsRepository::class
        );
        $this->app->bind(
            \App\Interfaces\ExternalProcedurePort::class,
            \App\Repositories\ExternalProceduresRepository::class
        );
        $this->app->bind(
            \App\Interfaces\ProfesionalSenderPort::class,
            \App\Repositories\ProfesionalSenderRepository::class
        );
        $this->app->bind(
            \App\Kafka\Ports\QueuePort::class,
            \App\Services\QueueService::class
        );
        $this->app->bind(
            \App\Interfaces\TarifePort::class,
            \App\Repositories\TarifeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
