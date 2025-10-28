<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Prometheus\PrometheusService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton( PrometheusService::class, PrometheusService::class );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
