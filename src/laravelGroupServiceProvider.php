<?php

namespace NovinVision\laravelGroup;

use Illuminate\Support\ServiceProvider;

class laravelGroupServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'laravel-group');

        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-group.php', 'laravel-group');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
