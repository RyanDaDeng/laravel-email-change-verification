<?php

namespace TimeHunter\LaravelEmailChangeVerification;

use Illuminate\Support\ServiceProvider;

class LaravelEmailChangeVerificationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'timehunter');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'timehunter');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelemailchangeverification.php', 'laravelemailchangeverification');

        // Register the service the package provides.
        $this->app->singleton('laravelemailchangeverification', function ($app) {
            return new LaravelEmailChangeVerification;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelemailchangeverification'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelemailchangeverification.php' => config_path('laravelemailchangeverification.php'),
        ], 'laravelemailchangeverification.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/timehunter'),
        ], 'laravelemailchangeverification.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/timehunter'),
        ], 'laravelemailchangeverification.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/timehunter'),
        ], 'laravelemailchangeverification.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
