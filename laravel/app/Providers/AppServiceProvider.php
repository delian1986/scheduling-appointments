<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Cache\AppointmentCacheManager;
use App\Repositories\Cache\CachingAppointmentRepository;
use App\Repositories\Cache\CachingClientRepository;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\ClientRepository;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->extend(
            ClientRepositoryInterface::class,
            fn (ClientRepositoryInterface $repository, $app): CachingClientRepository => new CachingClientRepository(
                $repository,
                $app->make(CacheRepository::class),
                $app->make(AppointmentCacheManager::class),
            ),
        );

        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->extend(
            AppointmentRepositoryInterface::class,
            fn (AppointmentRepositoryInterface $repository, $app): CachingAppointmentRepository => new CachingAppointmentRepository(
                $repository,
                $app->make(CacheRepository::class),
                $app->make(AppointmentCacheManager::class),
            ),
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
