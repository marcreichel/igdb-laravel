<?php

namespace MarcReichel\IGDBLaravel;

use Illuminate\Support\ServiceProvider;

class IGDBLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('igdb.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', 'igdb'
        );
    }
}
