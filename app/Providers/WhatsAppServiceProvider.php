<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('whatsapp', function ($app) {
            return new \App\Services\WhatsAppService();
        });
    }

    public function boot()
    {
        //
    }
}
