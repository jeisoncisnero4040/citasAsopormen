<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\PqrRepositoryInterface;
use App\Repositories\PqrsRepository;
use App\Interfaces\ReasonsPqrRepositoryInterface;
use App\Repositories\ReasonsPqrRepository;
use App\Repositories\UtilityRepository;
use App\Interfaces\UtilsPqrRepositoryInterface;
use App\Interfaces\AnalitycsRepositoryInterface;
use App\Interfaces\AuthInterface;
use App\Repositories\AnalitycsRepository;
use App\Repositories\LoginRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PqrRepositoryInterface::class, PqrsRepository::class);
        $this->app->bind(ReasonsPqrRepositoryInterface::class, ReasonsPqrRepository::class);
        $this->app->bind(UtilsPqrRepositoryInterface::class, UtilityRepository::class);
        $this->app->bind(AnalitycsRepositoryInterface::class, AnalitycsRepository::class);
        $this->app->bind(AuthInterface::class,LoginRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
