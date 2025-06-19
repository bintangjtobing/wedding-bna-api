<?php

namespace App\Providers;

use App\Services\DeviceDetectionService;
use Illuminate\Support\ServiceProvider;

class DeviceDetectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DeviceDetectionService::class, function ($app) {
            return new DeviceDetectionService();
        });

        $this->app->bind('device-detection', function ($app) {
            return new DeviceDetectionService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
