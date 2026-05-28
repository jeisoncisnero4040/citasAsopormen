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
            \App\Repositories\AuditRepository::class,
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
        $this->app->bind(
            \App\Interfaces\AuthsDocumentsPort::class,
            \App\Repositories\AuthsDocumentsRepository::class
        );
        $this->app->bind(
            \App\Interfaces\UserPort::class,
            \App\Repositories\UserRepository::class
        );
        $this->app->bind(
            \App\Interfaces\CapacitacionMediaPort::class,
            \App\Repositories\CapacitacionMediaRepository::class
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
