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
            \App\Repositories\AuditRepository::class
        );
        $this->app->bind(
            \App\Interfaces\StorageInterface::class,
            \App\Services\StorageService::class
        );
        $this->app->bind(
            \App\Interfaces\AuthsInterface::class,
            \App\Repositories\AuthsRepository::class
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
