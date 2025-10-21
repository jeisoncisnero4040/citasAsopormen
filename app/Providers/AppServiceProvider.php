<?php

namespace App\Providers;

use App\Services\Prometheus\PrometheusService;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    
    {   
        $this->app->singleton( PrometheusService::class, PrometheusService::class );
        $this->app->bind(
            \App\Interfaces\ProfesionalRepositoryInterface::class,
            \App\Repositories\ProfesionalRepository::class
        );

        $this->app->bind(
            \App\Interfaces\JwtInterface::class,
            \App\Services\JwtService::class
        );
        $this->app->bind(
            \App\Interfaces\AppoimentsRepositoryInterface::class,
            \App\Repositories\AppoimentRepository::class
        );
        $this->app->bind(
            \App\Interfaces\AuditInterface::class,
            \App\Repositories\AuditRepository::class
        );
        $this->app->bind(
            \App\Interfaces\AuthsInterface::class,
            \App\Repositories\AuthsRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\UtilitiesInterface::class,
            \App\Repositories\UtilitiesRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\ProcediproRepositoryInterface::class,
            \App\Repositories\ProcediproRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\LogsRepositoryInterface::class,
            \App\Repositories\LogsRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\RolesAndPermissionsRepositoryInterface::class,
            \App\Repositories\RolesAndPermissionsRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\EvoRepositoryInterface::class,
            \App\Repositories\EvoRepository::class,
        );
        $this->app->bind(
            \App\Interfaces\ClientRepositoryInterface::class,
            \App\Repositories\ClientRepository::class,
        );
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');
    }
}
